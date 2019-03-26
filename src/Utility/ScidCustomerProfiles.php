<?php


namespace Scid\Utility;


use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\ORM\Table;
use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class ScidCustomerProfiles
{


    private $_errors = [];

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'type'    => 'AuthorizeDotNet',
        'sandbox' => TRUE,
    ];

    protected $_options = [];

    protected $_sandbox = TRUE;

    const TRANSACTION_ID_PREFIX_KEY = 'id-prefix';
    const TRANSACTION_TRANSACTIONKEY_KEY = 'transaction-key';
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



    public function initialize(array $config) {
        $scid = Configure::read('Scid.payment');
        $type = $this->_defaultConfig['type'];
        if (!empty($scid['default_type'])) {
            $type = $scid['default_type'];
        }
        if (!empty($config['type'])) {
            $type = $config['type'];
        }
        $this->_options = $scid[$type];
        if (isset($scid['sandbox'])) {
            $this->_sandbox = $scid['sandbox'];
        }
        if (!empty($config['sandbox'])) {
            $this->_sandbox = $config['sandbox'];
        }
    }

    /**
     * @param \App\Model\Entity\Member|\Cake\Datasource\EntityInterface $member
     * @return bool|\net\authorize\api\contract\v1\CustomerProfileType
     */
    public function create($member) {
        if (!empty($member->auth_net_customer_id)) {
            $return = $this->get($member);
        }

    }

    public function get($member, $includePaymentProfiles = false) {
        $merchantAuthentication = new AnetAPI\MerchantAuthenticationType();
        $merchantAuthentication->setName(\SampleCodeConstants::MERCHANT_LOGIN_ID);
        $merchantAuthentication->setTransactionKey(\SampleCodeConstants::MERCHANT_TRANSACTION_KEY);

        // Set the transaction's refId
        $refId = 'ref' . time();
    }
}


