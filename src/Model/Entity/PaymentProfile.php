<?php

    namespace Scid\Model\Entity;

    use Cake\Core\Configure;
    use Cake\I18n\FrozenDate;
    use Cake\I18n\FrozenTime;
    use Cake\ORM\Entity;
    use Cake\ORM\TableRegistry;
    use Scid\Utility\ScidPaymentsTrait;

    /**
     * ScidPaymentProfile Entity
     *
     * @property string                             $id
     * @property \Cake\I18n\FrozenTime              $created
     * @property \Cake\I18n\FrozenTime              $modified
     * @property int                                $member_id
     * @property string                             $customer_profile_id
     * @property string                             $payment_profile_id
     * @property bool                               $primary
     * @property string                             $card_number
     * @property string                             $expiration_date
     * @property string                             $card_type
     *
     * @property string                             $first
     * @property string                             $last
     * @property string                             $address
     * @property string                             $city
     * @property string                             $state
     * @property string                             $zip
     * @property string                             $name
     * @property FrozenDate                         $expiration
     * @property FrozenTime                         $deleted
     *
     * @property \Cake\ORM\Entity                   $member
     * @property \Scid\Model\Entity\CustomerProfile $customer_profile
     * @property \Scid\Model\Entity\PaymentProfile  $payment_profile
     *
     *  virtual properties
     * @property int                                $number
     * @property string                             $expDate
     */

    use net\authorize\api\contract\v1 as AnetAPI;
    use net\authorize\api\controller as AnetController;

    /**
     * @property string                             $id
     * @property \Cake\I18n\FrozenTime              $created
     * @property \Cake\I18n\FrozenTime              $modified
     * @property int                                $member_id
     * @property string                             $customer_profile_id
     * @property string                             $payment_profile_id
     * @property bool                               $is_default
     * @property string                             $card_number
     * @property string                             $expiration_date
     * @property string                             $card_type
     * @property \Cake\ORM\Entity                   $member
     * @property \Scid\Model\Entity\CustomerProfile $customer_profile
     */
    class PaymentProfile extends Entity
    {

        use ScidPaymentsTrait;

        /**
         * Fields that can be mass assigned using newEntity() or patchEntity().
         *
         * Note that when '*' is set to true, this allows all unspecified fields to
         * be mass assigned. For security purposes, it is advised to set '*' to false
         * (or remove it), and explicitly make individual fields accessible as needed.
         *
         * @var array
         */
        protected $_accessible = [
            'created'             => TRUE,
            'modified'            => TRUE,
            'member_id'           => TRUE,
            'customer_profile_id' => TRUE,
            'payment_profile_id'  => TRUE,
            'primary'             => TRUE,
            'card_number'         => TRUE,
            'expiration_date'     => TRUE,
            'card_type'           => TRUE,
            'member'              => TRUE,
            'customer_profile'    => TRUE,
            'payment_profile'     => TRUE,
        ];

        public function __construct(array $properties = [], array $options = []) {
            parent::__construct($properties, $options);
            $this->_initialize([]);
        }

        public function updateFromRemote() {
            if (empty($this->customer_profile)) {
                $customerProfileTable = TableRegistry::getTableLocator()->get('Scid.CustomerProfiles');
                $this->customer_profile = $customerProfileTable->get($this->customer_profile_id);
            }
            $auth = $this->__getMerchantAuthentication();

            // Set the transaction's refId
            $refId = 'ref' . time();

            //request requires customerProfileId and customerPaymentProfileId
            $request = new AnetAPI\GetCustomerPaymentProfileRequest();
            $request->setMerchantAuthentication($auth);
            $request->setRefId($refId);
            $request->setCustomerProfileId($this->customer_profile->profile_id);
            $request->setCustomerPaymentProfileId($this->payment_profile_id);

            $controller = new AnetController\GetCustomerPaymentProfileController($request);
            /** @var \net\authorize\api\contract\v1\GetCustomerPaymentProfileResponse $response */
            $endPoint = $this->getEndpoint();

            $response = $controller->executeWithApiResponse($endPoint);
            if (($response != NULL)) {
                if ($response->getMessages()->getResultCode() == "Ok") {
                    $this->set('remoteProfile', $response->getPaymentProfile());
                    $this->set('card_number', $response->getPaymentProfile()->getPayment()->getCreditCard()->getCardNumber());
                    $this->set('card_type', $response->getPaymentProfile()->getPayment()->getCreditCard()->getCardType());
                    $this->set('first', $response->getPaymentProfile()->getBillTo()->getFirstName());
                    $this->set('last', $response->getPaymentProfile()->getBillTo()->getLastName());
                    $this->set('address', $response->getPaymentProfile()->getBillTo()->getAddress());
                    $this->set('city', $response->getPaymentProfile()->getBillTo()->getCity());
                    $this->set('state', $response->getPaymentProfile()->getBillTo()->getState());
                    $this->set('zip', $response->getPaymentProfile()->getBillTo()->getZip());
                    if ($response->getPaymentProfile()->getSubscriptionIds() != NULL) {
                        $subscriptionids = [];
                        if ($response->getPaymentProfile()->getSubscriptionIds() != NULL) {
                            foreach ($response->getPaymentProfile()->getSubscriptionIds() as $subscriptionid)
                                $subscriptionids[] = $subscriptionid;
                        }
                    }
                }
                else {

                    $errorMessages = $response->getMessages()->getMessage();
                    $this->setError('remoteProfile', [$errorMessages[0]->getCode() . "  " . $errorMessages[0]->getText()]);
                    return FALSE;
                }
            }
            else {
                $this->setError('remoteProfile', ["NULL Response Error"]);
                return FALSE;
            }
            return $response;
        }

        protected function _getName() {
            if (!empty($this->first) && !empty($this->last)) {
                return __('{0} {1}', [$this->first, $this->last]);
            }
        }

        protected function _getNumber() {
            $number = (integer)preg_replace("/[^0-9]/", "", $this->card_number);
            return $number;
        }

        /**
         * @return \Cake\I18n\FrozenDate|null
         */
        protected function _getExpiration() {
            if ($this->has('expiration_date') && !empty($this->_properties['expiration_date'])) {
                list($year, $month) = explode('-', $this->_properties['expiration_date']);
                $date = new FrozenDate();
                return $date->setDate($year, $month, 1);
            }
            else {
                return NULL;
            }
        }

        /**
         * @return string
         */
        protected function _getExpDate() {
            if ($this->has('expiration_date') && !empty($this->_properties['expiration_date'])) {
                $date = $this->_getExpiration();
                return $date->expiration->format('m/Y');
            }
            else {
                return '';
            }
        }
    }
