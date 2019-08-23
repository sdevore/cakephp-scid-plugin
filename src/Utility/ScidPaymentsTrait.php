<?php

    namespace Scid\Utility;

    use App\Model\Entity\Payment;
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
            $this->__loadErrorMap();
        }

        protected function __loadErrorMap() {
            $errorMapFile = Configure::load('Scid.responseCodeFieldMap');
        }

// private methods

        /**
         * @param $entity
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
                    $month = $entity->expiration_date->month;
                    $year = $entity->expiration_date->year;
                    //Log::debug( $year, 'payment_debug');
                    //Log::debug( $month, 'payment_debug');
                    $card->setExpirationDate($entity->expiration_date->format('Y-m'));
                }
                else if (!empty($entity->expMonth) && !empty($entity->expYear)) {
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
         * @return void
         */
        protected function __setError($entity, $errorCode, $errorText): void {
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
                default:
                    $errorCodes = Configure::read('Scid.payment_errors');
                    if (empty($errorCodes[$errorCode])) {
                        $entity->setError('credit_card_number', [
                            __('{0}: {1}',
                               [
                                   $errorCode,
                                   $errorText[0],
                               ]),
                        ]);
                    }
                    else {
                        $codeMap = $errorCodes[$errorCode];
                        if (empty($errorText)) {
                            $errorText = $codeMap->text;
                        }
                        if (empty($codeMap->fields)) {
                            foreach ($codeMap->fields as $field) {
                                $entity->setError($field, $errorText);
                            }
                        }
                    }
            }
            if (empty($errorCodes)) {
                $errorCodes = Configure::read('Scid.payment_errors');
            }
            if (!empty($errorCodes[$errorCode])) {
                $codeMap = $errorCodes[$errorCode];
                if (empty($errorText)) {
                    $errorText = $codeMap->text;
                }
                if (empty($codeMap->fields)) {
                    foreach ($codeMap->fields as $field) {
                        if (empty($entity->getError($field))) {
                            $entity->setError($field, $errorText);
                        }
                    }
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

    interface ScidPaymentsInterface
    {

        const TRANSACTION_ID_PREFIX_KEY = 'id-prefix';
        const TRANSACTION_TRANSACTION_KEY_KEY = 'transaction-key';
        const TRANSACTION_TYPE_KEY = 'transactionType';
        const TRANSACTION_TYPE_AUTHORIZE = 'authOnlyTransaction';
        const TRANSACTION_TYPE_AUTH_CAPTURE = 'authCaptureTransaction';
        const TRANSACTION_TYPE_CAPTURE = 'priorAuthCaptureTransaction';
        const TRANSACTION_TYPE_VOID = 'voidTransaction';
        const TRANSACTION_TYPE_REFUND = 'refundTransaction';

        const STATE_PENDING = 'Pending';
        const STATE_APPROVED = 'Approved';
        const STATE_FAILED = 'Failed';
        const STATE_CAPTURED = 'Captured';
        const STATE_SETTLED = 'Settled';
        const STATE_VOIDED = 'Voided';
        const STATE_REFUNDED = 'Refunded';

    }
