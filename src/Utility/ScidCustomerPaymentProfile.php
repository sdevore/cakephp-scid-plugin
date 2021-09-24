<?php


namespace Scid\Utility;

use net\authorize\api\contract\v1 as AnetAPI;
use net\authorize\api\controller as AnetController;

class ScidCustomerPaymentProfile
{
    /**
     * @var \net\authorize\api\contract\v1\CustomerProfilePaymentType
     */
    private $customerPaymentProfile;

    /**
     * @return \net\authorize\api\contract\v1\CustomerProfilePaymentType
     */
    public function getCustomerPaymentProfile(): \net\authorize\api\contract\v1\CustomerProfilePaymentType {
        return $this->customerPaymentProfile;
    }

    /**
     * @param \net\authorize\api\contract\v1\CustomerProfilePaymentType $customerPaymentProfile
     */
    public function setCustomerPaymentProfile(\net\authorize\api\contract\v1\CustomerProfilePaymentType $customerPaymentProfile): void {
        $this->customerPaymentProfile = $customerPaymentProfile;
    }


}
