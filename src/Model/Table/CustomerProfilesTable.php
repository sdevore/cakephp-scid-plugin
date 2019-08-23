<?php

namespace Scid\Model\Table;

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
 * CustomerProfiles Model
 *
 * @property \App\Model\Table\MembersTable|\Cake\ORM\Association\BelongsTo        $Members
 * @property \Scid\Model\Table\ProfilesTable|\Cake\ORM\Association\BelongsTo      $Profiles
 *
 * @method \Scid\Model\Entity\CustomerProfile get($primaryKey, $options = [])
 * @method \Scid\Model\Entity\CustomerProfile newEntity($data = null, array $options = [])
 * @method \Scid\Model\Entity\CustomerProfile[] newEntities(array $data, array $options = [])
 * @method \Scid\Model\Entity\CustomerProfile|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Scid\Model\Entity\CustomerProfile|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Scid\Model\Entity\CustomerProfile patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Scid\Model\Entity\CustomerProfile[] patchEntities($entities, array $data, array $options = [])
 * @method \Scid\Model\Entity\CustomerProfile findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 * @property \Scid\Model\Table\PaymentProfilesTable|\Cake\ORM\Association\HasMany $PaymentProfiles
 */
class CustomerProfilesTable extends Table
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

        $this->setTable('scid_customer_profiles');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Members', [
            'foreignKey' => 'member_id',
            'joinType'   => 'INNER',
            'className'  => 'Members',
        ]);
        $this->hasMany('Scid.PaymentProfiles')
             ->setForeignKey('customer_profile_id')
             ->setDependent(TRUE);;
    }

    public function beforeSave(Event $event, EntityInterface $entity, ArrayObject $options) {
        if ($entity->isNew()) {
            if (!$this->createCustomerProfile($entity)) {
                $event->stopPropagation();
            }
        }
    }

    /**
     * @param \Scid\Model\Entity\CustomerProfile|EntityInterface|\Scid\Model\Entity\CustomerProfile $customer_profile
     *
     * @return \Scid\Model\Entity\CustomerProfile|bool
     */
    protected function createCustomerProfile($customer_profile) {
        if (empty($customer_profile->member)) {
            $customer_profile->member = $this->Members->get($customer_profile->member_id);
        }
        $merchantAuthentication = $this->__getMerchantAuthentication();

        // Set the transaction's refId
        $refId = 'ref' . time();
        $customerProfile = new AnetAPI\CustomerProfileType();
        $customerProfile->setDescription(__('{0}', [$customer_profile->member->name]));
        $customerProfile->setMerchantCustomerId($customer_profile->member->id);
        $customerProfile->setEmail($customer_profile->member->email);

        // check for existing profile

        $request = new AnetAPI\GetCustomerProfileRequest();
        $request->setMerchantAuthentication($merchantAuthentication);
        $request->setEmail($customer_profile->member->email);
        $controller = new AnetController\GetCustomerProfileController($request);
        /** @var \net\authorize\api\contract\v1\GetCustomerPaymentProfileResponse $response */
        $response = $controller->executeWithApiResponse($this->getEndpoint());
        if (($response != null) && ($response->getMessages()->getResultCode() == "Ok") ) {
            /** @var \net\authorize\api\contract\v1\CustomerPaymentProfileType $profileSelected */
            $profileSelected = $response->getProfile();
            $customer_profile->profile_id = $profileSelected->getCustomerProfileId();
        }
        else {
            // Assemble the complete transaction request
            $request = new AnetAPI\CreateCustomerProfileRequest();
            $request->setMerchantAuthentication($merchantAuthentication);
            $request->setRefId($refId);
            $request->setProfile($customerProfile);

            // Create the controller and get the response
            $controller = new AnetController\CreateCustomerProfileController($request);
            /** @var \net\authorize\api\contract\v1\CreateCustomerProfileResponse $response */
            $response = $controller->executeWithApiResponse($this->getEndpoint());

            if (($response != NULL) && ($response->getMessages()->getResultCode() == "Ok")) {
                $customer_profile->profile_id = $response->getCustomerProfileId();

            } else {
                $errorMessages = $response->getMessages()->getMessage();
                $customer_profile->setError('create_customer_profile', [$errorMessages[0]->getCode() . "  " . $errorMessages[0]->getText()]);
                return FALSE;
            }
        }
        return $customer_profile;
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
            ->email('email')
            ->requirePresence('email', 'create')
            ->notEmpty('email');

        $validator
            ->scalar('config')
            ->maxLength('config', 20)
            ->requirePresence('config', 'create')
            ->notEmpty('config');

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
        $rules->add($rules->isUnique(['email']));
        $rules->add($rules->existsIn(['member_id'], 'Members'));
        return $rules;
    }
}
