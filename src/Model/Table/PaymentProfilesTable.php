<?php

    namespace Scid\Model\Table;

    use App\Model\Entity\Payment;
    use ArrayObject;
    use Cake\Datasource\EntityInterface;
    use Cake\Event\Event;
    use Cake\ORM\Query;
    use Cake\ORM\RulesChecker;
    use Cake\ORM\Table;
    use Cake\Validation\Validator;
    use Scid\Utility\ScidPaymentsTrait;
    use net\authorize\api\contract\v1 as AnetAPI;
    use net\authorize\api\controller as AnetController;

    /**
     * PaymentProfiles Model
     *
     * @property \Cake\ORM\Table|\Cake\ORM\Association\BelongsTo                         $Members
     * @property \Scid\Model\Table\CustomerProfilesTable|\Cake\ORM\Association\BelongsTo $CustomerProfiles
     * @property \Scid\Model\Table\PaymentProfilesTable|\Cake\ORM\Association\BelongsTo  $PaymentProfiles
     *
     * @method \Scid\Model\Entity\PaymentProfile get($primaryKey, $options = [])
     * @method \Scid\Model\Entity\PaymentProfile newEntity($data = NULL, array $options = [])
     * @method \Scid\Model\Entity\PaymentProfile[] newEntities(array $data, array $options = [])
     * @method \Scid\Model\Entity\PaymentProfile|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
     * @method \Scid\Model\Entity\PaymentProfile|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
     * @method \Scid\Model\Entity\PaymentProfile patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
     * @method \Scid\Model\Entity\PaymentProfile[] patchEntities($entities, array $data, array $options = [])
     * @method \Scid\Model\Entity\PaymentProfile findOrCreate($search, callable $callback = NULL, $options = [])
     *
     * @mixin \Cake\ORM\Behavior\TimestampBehavior
     * @mixin \Tools\Model\Behavior\ToggleBehavior
     */
    class PaymentProfilesTable extends Table
    {

        use ScidPaymentsTrait;

        /**
         * Initialize method
         *
         * @param array $config The configuration for the Table.
         * @return void
         */
        public function initialize(array $config) {
            parent::initialize($config);

            $this->setTable('scid_payment_profiles');
            $this->setDisplayField('id');
            $this->setPrimaryKey('id');

            $this->addBehavior('Timestamp');
            $this->addBehavior('Tools.Toggle', [
                'field'       => 'is_default',
                'scopeFields' => ['customer_profile_id'],
                'scope'       => [],
            ]);
            $this->addBehavior('Muffin/Trash.Trash');
            $this->belongsTo('Members', [
                'foreignKey' => 'member_id',
                'joinType'   => 'INNER',
                'className'  => 'Members',
            ]);
            $this->belongsTo('CustomerProfiles', [
                'foreignKey' => 'customer_profile_id',
                'joinType'   => 'INNER',
                'className'  => 'Scid.CustomerProfiles',
            ]);
        }

        public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options) {
            if ($entity->isNew()) {
                if (!$this->createProfile($entity)) {
                    $event->stopPropagation();
                    return FALSE;
                }
            }
            else {
                if (!$entity->deleted && !$this->updateProfile($entity)) {
                    $event->stopPropagation();
                    return FALSE;
                }
            }
        }

        public function beforeDelete(Event $event, EntityInterface $entity, ArrayObject $options) {
            if (!$this->deleteProfile($entity)) {
                $event->stopPropagation();
            }
        }

        /**
         * @param Payment $payment
         * @param $paymentProfile
         * @param $data
         * @return bool
         */
        public function addPaymentProfileFromPayment($payment, $paymentProfile, $data) {

            // check for existing customer profile for member email
            if (empty($payment->member) || (empty($payment->member->customer_profiles) || empty($payment->member->payment_profile))) {
                $member = $this->Members->get($payment->member_id, ['contain'=>['PaymentProfiles','CustomerProfiles']]);
            }
            $customerProfile = null;
            if (!empty($member->customer_profiles)) {
                $customer_profile = $member->customer_profiles[0];
                $customerProfile = $customer_profile->getRemote();
            }
            else {
                // see if there is already one online that matches email address (because they could be created manually
                $customerProfile = $this->CustomerProfiles->getRemoteFromEmail($member->email, $member->id);
            }
            if (empty($customerProfile)) {
                $paymentProfile->setError('default', __('Customer Profile could not be found or created'));
                return false;
            }
            $merchantAuthentication = $this->__getMerchantAuthentication();
            // Set the transaction's refId
            $refId = 'ref' . time();
            $request = new AnetAPI\CreateCustomerProfileFromTransactionRequest();
            $request->setMerchantAuthentication($merchantAuthentication);
            $request->setTransId($payment->transactionNumber);
            $request->setCustomer($customerProfile);
            $controller = new AnetController\CreateCustomerProfileFromTransactionController($request);

            /** @var \net\authorize\api\contract\v1\CreateCustomerProfileResponse $response */
            $response = $controller->executeWithApiResponse($this->getEndpoint());
            if (($response != NULL) && ($response->getMessages()->getResultCode() == "Ok")) {
                /** @var \Scid\Model\Table\CustomerProfilesTable $customerProfiles */
                $profile_id = $response->getCustomerProfileId();
                if (!empty($response->getCustomerPaymentProfileIdList())) {
                    $payment_profile_id_list = $response->getCustomerPaymentProfileIdList();
                    $id = array_shift($payment_profile_id_list);
                    $paymentProfile = $this->newEntity([
                                                                                 'member_id'           => $payment->member->id,
                                                                                 'customer_profile_id' => $profile_id,
                                                                                 'payment_profile_id'  => $id,
                                                                                 'card_number'         => $payment->credit_card_number,
                                                                                 'expiration_date'     => $payment->expDate,
                                                                                 'is_default'          => TRUE,
                                                                             ]);
                    if ($this->save($payment, ['validation'=>false])) {
                        return $paymentProfile;
                    }
                    else {
                        return false;
                    }

                }
            } else {
                $errorMessages = $response->getMessages()->getMessage();
                $errorCode = $errorMessages[0]->getErrorCode();
                $errorText = $errorMessages[0]->getErrorText();
                $result['error_code'] = $errorCode;
                $result['error_message'] = $errorText;
                $this->__setError($paymentProfile, $errorCode, $errorText);
            }
        }

        /**
         * @param EntityInterface|\Scid\Model\Entity\PaymentProfile $entity
         *
         * @return bool|EntityInterface
         */
        protected function createProfile($entity) {
            $merchantAuthentication = $this->__getMerchantAuthentication();
            // Set the transaction's refId
            $refId = 'ref' . time();

            // Create a Customer Profile Request
            //  1. (Optionally) create a Payment Profile
            //  2. (Optionally) create a Shipping Profile
            //  3. Create a Customer Profile (or specify an existing profile)
            //  4. Submit a CreateCustomerProfile Request
            //  5. Validate Profile ID returned

            // Set credit card information for payment profile
            $creditCard = $this->__getAuthorizePayment($entity);

            // Create a new Customer Payment Profile object
            $paymentprofile = new AnetAPI\CustomerPaymentProfileType();
            $paymentprofile->setCustomerType('individual');

            // Create the Bill To info for new payment type
            if (!empty($entity->form_data)) {
                $formData = $entity->form_data;
                $billto = new AnetAPI\CustomerAddressType();
                if (!empty($formData['first'])) {
                    $billto->setFirstName($formData['first']);
                }
                if (!empty($formData['last'])) {
                    $billto->setLastName($formData['last']);
                }
                if (!empty($formData['address'])) {
                    $billto->setAddress($formData['address']);
                }
                if (!empty($formData['city'])) {
                    $billto->setCity($formData['city']);
                }
                if (!empty($formData['state'])) {
                    $billto->setState($formData['state']);
                }
                if (!empty($formData['zip'])) {
                    $billto->setZip($formData['zip']);
                }
                if (!empty($formData['phone'])) {
                    $billto->setPhoneNumber($formData['phone']);
                }
                $paymentprofile->setBillTo($billto);
            }
            $paymentprofile->setPayment($creditCard);
            $paymentprofile->setDefaultPaymentProfile($entity->is_default);

            $paymentprofiles[] = $paymentprofile;

            // Assemble the complete transaction request
            $paymentprofilerequest = new AnetAPI\CreateCustomerPaymentProfileRequest();
            $paymentprofilerequest->setMerchantAuthentication($merchantAuthentication);

            // Add an existing profile id to the request
            $paymentprofilerequest->setCustomerProfileId($entity->customer_profile->profile_id);
            $paymentprofilerequest->setPaymentProfile($paymentprofile);
            $paymentprofilerequest->setValidationMode("liveMode");

            // Create the controller and get the response
            $controller = new AnetController\CreateCustomerPaymentProfileController($paymentprofilerequest);
            /** @var \net\authorize\api\contract\v1\CreateCustomerPaymentProfileResponse $response */
            $response = $controller->executeWithApiResponse($this->getEndpoint());

            if (($response != NULL) && ($response->getMessages()->getResultCode() == "Ok")) {
                $entity->payment_profile_id = $response->getCustomerPaymentProfileId();
            }
            else {

                $errorMessages = $response->getMessages()->getMessage();
                $entity->setError('payment_profile', $errorMessages[0]->getCode() . "  " . $errorMessages[0]->getText());
                $this->__setError($entity, $errorMessages[0]->getCode(), $errorMessages[0]->getText());
                return FALSE;
            }
            return $entity;
        }

        /**
         * @param \Scid\Model\Entity\PaymentProfile|EntityInterface $entity
         *
         * @return boolean
         */
        protected function updateProfile($entity) {
            $merchantAuthentication = $this->__getMerchantAuthentication();
            // Set the transaction's refId

            /* Create a merchantAuthenticationType object with authentication details
               retrieved from the constants file */

            // Set the transaction's refId
            $refId = 'ref' . time();

            $request = new AnetAPI\GetCustomerPaymentProfileRequest();
            $request->setMerchantAuthentication($merchantAuthentication);
            $request->setRefId($refId);
            $request->setCustomerProfileId($entity->customer_profile->profile_id);
            $request->setCustomerPaymentProfileId($entity->payment_profile_id);

            $controller = new AnetController\GetCustomerPaymentProfileController($request);
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
            if (($response != NULL) && ($response->getMessages()->getResultCode() == "Ok")) {
                $paymentprofile = new AnetAPI\CustomerPaymentProfileExType();
                $paymentprofile->setCustomerPaymentProfileId($entity->payment_profile_id);
                // Set credit card information for payment profile
                $creditCard = $this->__getAuthorizePayment($entity);

                if (!empty($entity->form_data)) {
                    $formData = $entity->form_data;
                    $billto = new AnetAPI\CustomerAddressType();
                    if (!empty($formData['first'])) {
                        $billto->setFirstName($formData['first']);
                    }
                    if (!empty($formData['last'])) {
                        $billto->setLastName($formData['last']);
                    }
                    if (!empty($formData['address'])) {
                        $billto->setAddress($formData['address']);
                    }
                    if (!empty($formData['city'])) {
                        $billto->setCity($formData['city']);
                    }
                    if (!empty($formData['state'])) {
                        $billto->setState($formData['state']);
                    }
                    if (!empty($formData['zip'])) {
                        $billto->setZip($formData['zip']);
                    }
                    if (!empty($formData['phone'])) {
                        $billto->setPhoneNumber($formData['phone']);
                    }
                    $paymentprofile->setBillTo($billto);
                }
                $paymentprofile->setPayment($creditCard);

                $paymentprofile->setCustomerPaymentProfileId($entity->payment_profile_id);
                $paymentprofile->setPayment($creditCard);

                // Submit a UpdatePaymentProfileRequest
                $request = new AnetAPI\UpdateCustomerPaymentProfileRequest();
                $request->setMerchantAuthentication($merchantAuthentication);
                $request->setCustomerProfileId($entity->customer_profile->profile_id);
                $request->setPaymentProfile($paymentprofile);

                $controller = new AnetController\UpdateCustomerPaymentProfileController($request);
                $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
                if (($response != NULL) && ($response->getMessages()->getResultCode() == "Ok")) {
                    return TRUE;
                }
                else if ($response != NULL) {
                    $errorMessages = $response->getMessages()->getMessage();
                    $entity->setError('paymentProfile', [$errorMessages[0]->getCode() . "  " . $errorMessages[0]->getText()]);
                    return FALSE;
                }
            }
            else {
                $errorMessages = $response->getMessages()->getMessage();
                $entity->setError('paymentProfile', [$errorMessages[0]->getCode() . "  " . $errorMessages[0]->getText()]);
                $this->__setError($entity, $errorMessages[0]->getCode(), $errorMessages[0]->getText());
                return FALSE;
            }
        }

        /**
         * @param \Scid\Model\Entity\PaymentProfile|EntityInterface $entity
         *
         * @return boolean
         */
        protected function deleteProfile($entity) {
            $merchantAuthentication = $this->__getMerchantAuthentication();

            // Set the transaction's refId
            $refId = 'ref' . time();

            // Use an existing payment profile ID for this Merchant name and Transaction key

            $request = new AnetAPI\DeleteCustomerPaymentProfileRequest();
            $request->setMerchantAuthentication($merchantAuthentication);
            $request->setCustomerProfileId($entity->customer_profile->profile_id);
            $request->setCustomerPaymentProfileId($entity->payment_profile_id);
            $controller = new AnetController\DeleteCustomerPaymentProfileController($request);
            $response = $controller->executeWithApiResponse(\net\authorize\api\constants\ANetEnvironment::SANDBOX);
            if (($response != NULL) && ($response->getMessages()->getResultCode() == "Ok")) {
                return TRUE;
            }
            else {
                $errorMessages = $response->getMessages()->getMessage();
                $this->__setError($entity, $errorMessages[0]->getCode(), $errorMessages[0]->getText());
                return FALSE;
            }
        }

        public function findForMember(Query $query, array $options) {
            $member = $options['member'];
            $query = $query->matching('CustomerProfiles', function ($q) use ($member) {
                return $q->where(['CustomerProfiles.member_id' => $member->id]);
            });
            return $query->formatResults(function (\Cake\Collection\CollectionInterface $results) {
                return $results->map(function ($paymentProfile) {
                    /** @var \Scid\Model\Entity\PaymentProfile $paymentProfile */
                    $paymentProfile->updateFromRemote();
                    return $paymentProfile;
                });
            });
        }

        /**
         * Default validation rules.
         *
         * @param \Cake\Validation\Validator $validator Validator instance.
         * @return \Cake\Validation\Validator
         */
        public function validationDefault(Validator $validator) {
            $validator
                ->uuid('id')
                ->allowEmpty('id', 'create');

            $validator
                ->boolean('is_default');

            $validator
                ->scalar('card_number')
                ->maxLength('card_number', 64)
                ->requirePresence('card_number', 'create')
                ->notEmpty('card_number');

            $validator
                ->scalar('expiration_date')
                ->maxLength('expiration_date', 7)
                ->requirePresence('expiration_date', 'create')
                ->notEmpty('expiration_date');

            return $validator;
        }

        /**
         * Returns a rules checker object that will be used for validating
         * application integrity.
         *
         * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
         * @return \Cake\ORM\RulesChecker
         */
        public function buildRules(RulesChecker $rules) {
            $rules->add($rules->existsIn(['member_id'], 'Members'));
            $rules->add($rules->existsIn(['customer_profile_id'], 'CustomerProfiles'));

            return $rules;
        }
    }
