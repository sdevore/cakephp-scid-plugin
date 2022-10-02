<?php

    namespace Scid\Utility;

    use App\Model\Entity\Payment;
    use Cake\Chronos\Date;
    use Cake\Core\Configure;
    use Cake\Datasource\EntityInterface;
    use Cake\Filesystem\File;
    use Cake\I18n\FrozenDate;
    use net\authorize\api\contract\v1 as AnetAPI;
    use net\authorize\api\controller as AnetController;
    use Scid\Model\Entity\CustomerProfile;
    use Scid\Model\Entity\PaymentProfile;

    trait ScidPaymentsTrait
    {

        protected $_defaultPaymentConfig = [
            'type'        => 'AuthorizeDotNet',
            'credentials' => 'default',
            'sandbox'     => TRUE,
        ];

        protected $_options = [];

        protected $_credentials = 'default';

        protected $_sandbox = TRUE;

        protected $_errorMap = NULL;

        public function _initialize(array $config) {
            $scid = Configure::read('Scid.payment');

            if (!empty($config['credentials'])) {
                $this->_credentials = $scid['credentials'];
            }
            $type = $this->_defaultPaymentConfig['type'];
            if (!empty($scid['default_type'])) {
                $type = $scid['default_type'];
            }
            if (!empty($config['type'])) {
                $type = $config['type'];
            }
            if (!empty($scid[$this->_credentials][$type])) {
                $this->_options = $scid[$this->_credentials][$type];
            }
            else {
                $this->_options = $scid[$this->_defaultPaymentConfig['credentials']][$type];
            }
            if (isset($scid['sandbox'])) {
                $this->_sandbox = $scid['sandbox'];
            }
            if (!empty($config['sandbox'])) {
                $this->_sandbox = $config['sandbox'];
            }
        }

        protected function __loadErrorMap() {
            $errorMapFile = Configure::load('Scid.responseCodeFieldMap');
        }

        /**
         * address verification error
         * @param $code
         *
         * @return mixed|string
         */
        protected function getAvsResultError($code) {
            $map = [
                'A' => 'The street address matched, but the postal code did not.',
                'B' => 'No address information was provided.', 'E' => 'The AVS check returned an error.',
                'G' => 'The card was issued by a bank outside the U.S. and does not support AVS.',
                'N' => 'Neither the street address nor postal code matched.',
                'P' => 'AVS is not applicable for this transaction.',
                'R' => 'Retry — AVS was unavailable or timed out.', 'S' => 'AVS is not supported by card issuer.',
                'U' => 'Address information is unavailable.',
                'W' => 'The US ZIP+4 code matches, but the street address does not.',
                'X' => 'Both the street address and the US ZIP+4 code matched.',
                'Y' => 'The street address and postal code matched.',
                'Z' => 'The postal code matched, but the street address did not.',
            ];
            if (!empty($map[$code])) {
                return $map[$code];
            }
            return 'unknown Address Verification Service (AVS) error';
        }

        protected function getCVVResultError($code) {
            $map = [
                'M' => 'CVV matched.', 'N' => 'CVV did not match.', 'P' => 'CVV was not processed.',
                'S' => 'CVV should have been present but was not indicated.',
                'U' => 'The issuer was unable to process the CVV check.',
            ];
            if (!empty($map[$code])) {
                return $map[$code];
            }
            return 'unknown Card code verification (CCV) error';
        }

        protected function getCavvResultError($code) {
            $map = ['0' => 'CAVV was not validated because erroneous data was submitted.','1' => 'CAVV failed validation.','2' => 'CAVV passed validation.','3' => 'CAVV validation could not be performed; issuer attempt incomplete.','4' => 'CAVV validation could not be performed; issuer system error.','5' => 'Reserved for future use.','6' => 'Reserved for future use.','7' => 'CAVV failed validation, but the issuer is available. Valid for U.S.-issued card submitted to non-U.S acquirer.','8' => 'CAVV passed validation and the issuer is available. Valid for U.S.-issued card submitted to non-U.S. acquirer.','9' => 'CAVV failed validation and the issuer is unavailable. Valid for U.S.-issued card submitted to non-U.S acquirer.','A' => 'CAVV passed validation but the issuer unavailable. Valid for U.S.-issued card submitted to non-U.S acquirer.','B' => 'CAVV passed validation, information only, no liability shift.',];
            if (!empty($map[$code])) {
                return $map[$code];
            }
            return 'unknown Cardholder authentication verification error';
        }

        /**
         * @return mixed
         */
        protected function getErrorMap() {
            if (!Configure::check('Scid.payment_errors')) {
                $this->__loadErrorMap();
            }
            return Configure::read('Scid.payment_errors');
        }

// private methods

        /**
         * @param EntityInterface|PaymentProfile $entity
         *
         * @return \net\authorize\api\contract\v1\PaymentType
         */
        protected function __getAuthorizePayment($entity): AnetAPI\PaymentType {
            $paymentOne = new AnetAPI\PaymentType();
            // create the credit card
            $validatePayment = FALSE;

            if ($entity->has('dataDescriptor') && $entity->has('dataValue')) {
                // use nonce / opaqueData
                $opaqueData = new AnetAPI\OpaqueDataType();
                $opaqueData->setDataDescriptor($entity->dataDescriptor);
                $opaqueData->setDataValue($entity->dataValue);
                $paymentOne->setOpaqueData($opaqueData);
                $validatePayment = TRUE;
            }
            else {
                $card = new AnetAPI\CreditCardType();
                if (!empty($entity->credit_card_number)) {
                    $number = preg_replace('/\D+/', '', $entity->credit_card_number);
                    $card->setCardNumber($number);
                    $entity->number = substr($number, -4);
                }
                else {
                    $entity->setError('credit_card_number', [__('credit card number is required')]);
                }
                if (!empty($entity->card_code)) {
                    $card->setCardCode($entity->card_code);
                }
                else {
                    $entity->setError('card_code', [__('credit card verification number is required')]);
                }
                if (!empty($entity->expiration_date)) {
                    if (is_string($entity->expiration_date)) {
                        $card->setExpirationDate($entity->expiration_date);
                    }
                    elseif ($entity->expiration_date instanceof Date) {
                        $month = $entity->expiration_date->month;
                        $year = $entity->expiration_date->year;
                        //Log::debug( $year, 'payment_debug');
                        //Log::debug( $month, 'payment_debug');
                        $card->setExpirationDate($entity->expiration_date->format('Y-m'));
                    }
                    else {
                        $card->setExpirationDate($entity->expiration_date);
                    }
                }
                elseif (!empty($entity->expMonth) && !empty($entity->expYear)) {
                    $month = $entity->expMonth;
                    $year = $entity->expYear;
                    $date = new FrozenDate();
                    $date = $date->setDate($year, $month, 1);
                    $card->setExpirationDate($date->format('Y-m'));
                }
                else {
                    $entity->setError('expiration_date', [__('no valid expiration date was set')]);
                }
                $paymentOne->setCreditCard($card);
            }
            return $paymentOne;
        }

        /**
         * @return AnetAPI\MerchantAuthenticationType
         */
        protected function __getMerchantAuthentication($credentials = NULL): AnetAPI\MerchantAuthenticationType {
            if (empty($this->_options)) {
                $this->_initialize([]);
            }
            $options = $this->_options;
            if (!empty($configuration)) {
                $options = $this->__options($credentials);
            }

            $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
            $merchantAuthentication->setName($options['login_id']);
            $merchantAuthentication->setTransactionKey($options['transaction_key']);
            return $merchantAuthentication;
        }

        protected function __options($credentials, $type = NULL) {
            if (empty($credentials)) {
                $credentials = $this->_defaultPaymentConfig['credentials'];
            }
            $scid = Configure::read('Scid.payment');
            if (empty($type)) {
                $type = $this->_defaultPaymentConfig['type'];
                if (!empty($scid['default_type'])) {
                    $type = $scid['default_type'];
                }
            }
            if (!empty($scid[$credentials][$type])) {
                $options = $scid[$credentials][$type];
            }
            else {
                $options = $scid[$this->_defaultPaymentConfig['credentials']][$type];
            }
            return $options;
        }

        /**
         * @param EntityInterface|Payment|PaymentProfile|CustomerProfile $entity
         * @param integer                                                $errorCode
         * @param string                                                 $errorText
         * @param AnetAPI\TransactionResponseType                        $response
         * @return void
         */
        protected function __setError($entity, $errorCode, $errorText, $response = NULL): void {
            $errorText = [$errorText];
            switch ($errorCode) {
                case 5:
                    $entity->setError('amountPaid', $errorText);
                    break;
                case 6:
                    $entity->setError('credit_card_number', $errorText);
                    break;
                case 7:
                case 8:
                    $entity->setError('expMonth', $errorText);
                    $entity->setError('expYear', $errorText);
                    break;
                case 9:
                    $entity->setError('routing_number', $errorText);
                    break;
                case 10:
                    $entity->setError('account_number', $errorText);
                    break;
                case 11:
                    $entity->setError('credit_card_number', $errorText);
                    break;
                case 12:
                    $entity->setError('card_code', $errorText);
                    break;
                case 65:
                    if (!empty($response)) {
                        $cvvResultCode = $response->getCvvResultCode();
                        if ($cvvResultCode == 'N') {
                            $entity->setError('card_code', __('CCV does not match'));
                        }
                        elseif ($cvvResultCode == 'S') {
                            $entity->setError('card_code', __('CCV should be on card but was not included'));
                        }
                        elseif ($cvvResultCode == 'U') {
                            $entity->setError('card_code', __('Issuer is not certified for CCV processing'));
                        }
                    }
                    else {
                        $entity->setError('credit_card_number', $errorText);
                    }
                    break;
                default:
                    $errorCodes = $this->getErrorMap();
                    if (empty($errorCodes[$errorCode])) {
                        if (is_array($errorText) and !empty($errorText)) {
                            $errorText = 'Problem with Credit Card numer';
                        }
                        try {
                            $entity->setError('credit_card_number', [
                                __('{0}: {1}',
                                   [
                                       $errorCode,
                                       $errorText[0],
                                   ]),
                            ]);
                        } catch (\Exception $e) {
                            $entity->setError('credit_card_number', [
                                __('Problem with Credit Card number'),
                            ]);
                        }
                    }
                    else {
                        $codeMap = $errorCodes[$errorCode];
                        if (empty($errorText)) {
                            $errorText = $codeMap->text;
                        }
                        if (!empty($response)) {
                            $cvvResultCode = $response->getCvvResultCode();
                            if ($cvvResultCode == 'N') {
                                $entity->setError('card_code', __('CCV does not match'));
                            }
                            elseif ($cvvResultCode == 'S') {
                                $entity->setError('card_code', __('CCV should be on card but was not included'));
                            }
                            elseif ($cvvResultCode == 'U') {
                                $entity->setError('card_code', __('Issuer is not certified for CCV processing'));
                            }
                        }
                        elseif (!empty($codeMap->fields)) {
                            foreach ($codeMap->fields as $field) {
                                $entity->setError($field, $errorText);
                            }
                        }
                        else {
                            $entity->setError('default', !empty($codeMap->description)?$codeMap->description:'unknown error');
                        }
                    }
            }
        }

        /**
         * @param Payment                         $payment
         * @param AnetAPI\TransactionResponseType $tresponse
         * @return Payment
         */
        protected function __setErrorFromTransactionResponse($payment, $tresponse) {
            if ($tresponse->getResponseCode() !== 1) {
                if ($tresponse->getAvsResultCode() !== 'Y') {
                    $payment->setError('address', $this->getAvsResultError($tresponse->getAvsResultCode()));
                }
                if ($tresponse->getCvvResultCode() !== 'M') {
                    $payment->setError('cvv', $this->getCVVResultError($tresponse->getCvvResultCode()));
                    $payment->setError('card_code', $this->getCVVResultError($tresponse->getCvvResultCode()));
                }
                if ($tresponse->getCavvResultCode() !== '2') {
                    $payment->setError('cavv', $this->getAvsResultError($tresponse->getAvsResultCode()));
                }
            }
        }

        protected function getEndpoint() {
            if ($this->_sandbox) {
                $endPoint = \net\authorize\api\constants\ANetEnvironment::SANDBOX;
            }
            else {
                $endPoint = \net\authorize\api\constants\ANetEnvironment::PRODUCTION;
            }
            return $endPoint;
        }
    }

