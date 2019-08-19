<?php


namespace Scid\Utility;

use Cake\Core\Configure;
use Cake\I18n\FrozenDate;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

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
        } else {
            $this->_options = $scid[$this->_defaultPaymentConfig['credentials']][$type];
        }
        if (isset($scid['sandbox'])) {
            $this->_sandbox = $scid['sandbox'];
        }
        if (!empty($config['sandbox'])) {
            $this->_sandbox = $config['sandbox'];
        }
    }

    /**
     * @return AnetAPI\MerchantAuthenticationType
     */
    protected function __getMerchantAuthentication($credentials = null): AnetAPI\MerchantAuthenticationType {
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
        } else {
            $card = new AnetAPI\CreditCardType();
            if (!empty($entity->credit_card_number)) {
                $number = preg_replace('/\D+/', '', $entity->credit_card_number);
                $card->setCardNumber($number);
                $entity->number = substr($number, -4);
            } else {
                $entity->setError('credit_card_number', [__('credit card number is required')]);
            }
            if (!empty($entity->card_code)) {
                $card->setCardCode($entity->card_code);
            } else {
                $entity->setError('card_code', [__('credit card verification number is required')]);
            }
            if (!empty($entity->expiration_date)) {
                $month = $entity->expiration_date->month;
                $year = $entity->expiration_date->year;
                //Log::debug( $year, 'payment_debug');
                //Log::debug( $month, 'payment_debug');
                $card->setExpirationDate($entity->expiration_date->format('Y-m'));
            } elseif (!empty($entity->expMonth) && !empty($entity->expYear)) {
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

    protected function __options($credentials, $type = null) {
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
        } else {
            $options = $scid[$this->_defaultPaymentConfig['credentials']][$type];
        }
        return $options;
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
