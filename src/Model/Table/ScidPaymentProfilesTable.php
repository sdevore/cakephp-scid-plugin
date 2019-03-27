<?php
namespace Scid\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ScidPaymentProfiles Model
 *
 * @property \Scid\Model\Table\MembersTable|\Cake\ORM\Association\BelongsTo $Members
 * @property \Scid\Model\Table\CustomerProfilesTable|\Cake\ORM\Association\BelongsTo $CustomerProfiles
 * @property \Scid\Model\Table\PaymentProfilesTable|\Cake\ORM\Association\BelongsTo $PaymentProfiles
 *
 * @method \Scid\Model\Entity\ScidPaymentProfile get($primaryKey, $options = [])
 * @method \Scid\Model\Entity\ScidPaymentProfile newEntity($data = null, array $options = [])
 * @method \Scid\Model\Entity\ScidPaymentProfile[] newEntities(array $data, array $options = [])
 * @method \Scid\Model\Entity\ScidPaymentProfile|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Scid\Model\Entity\ScidPaymentProfile|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Scid\Model\Entity\ScidPaymentProfile patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Scid\Model\Entity\ScidPaymentProfile[] patchEntities($entities, array $data, array $options = [])
 * @method \Scid\Model\Entity\ScidPaymentProfile findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ScidPaymentProfilesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('scid_payment_profiles');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Members', [
            'foreignKey' => 'member_id',
            'joinType' => 'INNER',
            'className' => 'Scid.Members'
        ]);
        $this->belongsTo('CustomerProfiles', [
            'foreignKey' => 'customer_profile_id',
            'joinType' => 'INNER',
            'className' => 'Scid.CustomerProfiles'
        ]);
        $this->belongsTo('PaymentProfiles', [
            'foreignKey' => 'payment_profile_id',
            'joinType' => 'INNER',
            'className' => 'Scid.PaymentProfiles'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->uuid('id')
            ->allowEmpty('id', 'create');

        $validator
            ->boolean('default')
            ->requirePresence('default', 'create')
            ->notEmpty('default');

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

        $validator
            ->scalar('card_type')
            ->maxLength('card_type', 64)
            ->requirePresence('card_type', 'create')
            ->notEmpty('card_type');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['member_id'], 'Members'));
        $rules->add($rules->existsIn(['customer_profile_id'], 'CustomerProfiles'));
        $rules->add($rules->existsIn(['payment_profile_id'], 'PaymentProfiles'));

        return $rules;
    }
}
