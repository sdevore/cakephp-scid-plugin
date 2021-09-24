<?php


namespace Scid\Utility;


use Cake\Core\Configure;
use Cake\ORM\TableRegistry;
use Cake\ORM\Table;
use Scid\Utility\ScidPaymentsTrait;
use Scid\Utility\ScidPaymentsInterface;


class ScidCustomerProfile implements ScidPaymentsInterface
{
    use ScidPaymentsTrait;

    /**
     * @var \net\authorize\api\contract\v1\CustomerProfileType
     */
    private $_customerProfile;



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
     * @return \net\authorize\api\contract\v1\CustomerProfileType
     */
    public function getCustomerProfile(): \net\authorize\api\contract\v1\CustomerProfileType {
        return $this->_customerProfile;
    }

    /**
     * @param \net\authorize\api\contract\v1\CustomerProfileType $customerProfile
     */
    public function setCustomerProfile(\net\authorize\api\contract\v1\CustomerProfileType $customerProfile): void {
        $this->_customerProfile = $customerProfile;
    }
}


