<?php


namespace Scid\Utility;

use Cake\Core\Configure;
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
    protected $_defaultConfig = [
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
        $type = $this->_defaultConfig['type'];
        if (!empty($scid['default_type'])) {
            $type = $scid['default_type'];
        }
        if (!empty($config['type'])) {
            $type = $config['type'];
        }
        if (!empty($scid[$this->_credentials][$type])) {
            $this->_options = $scid[$this->_credentials][$type];
        } else {
            $this->_options = $scid[$this->_defaultConfig['credentials']][$type];
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
        $options = $this->_options;
        if (!empty($configuration)) {
            $options = $this->__options($credentials);
        }
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName($options['login_id']);
        $merchantAuthentication->setTransactionKey($options['transaction_key']);
        return $merchantAuthentication;
    }

    protected function __options($credentials, $type = null) {
        if (empty($credentials)) {
            $credentials = $this->_defaultConfig['credentials'];
        }
        $scid = Configure::read('Scid.payment');
        if (empty($type)) {
            $type = $this->_defaultConfig['type'];
            if (!empty($scid['default_type'])) {
                $type = $scid['default_type'];
            }
        }
        if (!empty($scid[$credentials][$type])) {
            $options = $scid[$credentials][$type];
        } else {
            $options = $scid[$this->_defaultConfig['credentials']][$type];
        }
        return $options;
    }
}
