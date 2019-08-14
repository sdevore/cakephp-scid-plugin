<?php
namespace Scid\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * CustomerProfiles Model
 *
 * @property \App\Model\Table\MembersTable|\Cake\ORM\Association\BelongsTo $Members
 * @property \Scid\Model\Table\ProfilesTable|\Cake\ORM\Association\BelongsTo $Profiles
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

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('scid_customer_profiles');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Members', [
            'foreignKey' => 'member_id',
            'joinType' => 'INNER',
            'className' => 'Members'
        ]);
        $this->hasMany('Scid.PaymentProfiles')
            ->setForeignKey('customer_profile_id')
            ->setDependent(true);;
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
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->isUnique(['email']));
        $rules->add($rules->existsIn(['member_id'], 'Members'));
        return $rules;
    }
}
