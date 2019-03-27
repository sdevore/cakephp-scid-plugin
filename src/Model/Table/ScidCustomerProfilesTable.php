<?php
namespace Scid\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * ScidCustomerProfiles Model
 *
 * @property \Scid\Model\Table\MembersTable|\Cake\ORM\Association\BelongsTo $Members
 * @property \Scid\Model\Table\ProfilesTable|\Cake\ORM\Association\BelongsTo $Profiles
 *
 * @method \Scid\Model\Entity\ScidCustomerProfile get($primaryKey, $options = [])
 * @method \Scid\Model\Entity\ScidCustomerProfile newEntity($data = null, array $options = [])
 * @method \Scid\Model\Entity\ScidCustomerProfile[] newEntities(array $data, array $options = [])
 * @method \Scid\Model\Entity\ScidCustomerProfile|bool save(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Scid\Model\Entity\ScidCustomerProfile|bool saveOrFail(\Cake\Datasource\EntityInterface $entity, $options = [])
 * @method \Scid\Model\Entity\ScidCustomerProfile patchEntity(\Cake\Datasource\EntityInterface $entity, array $data, array $options = [])
 * @method \Scid\Model\Entity\ScidCustomerProfile[] patchEntities($entities, array $data, array $options = [])
 * @method \Scid\Model\Entity\ScidCustomerProfile findOrCreate($search, callable $callback = null, $options = [])
 *
 * @mixin \Cake\ORM\Behavior\TimestampBehavior
 */
class ScidCustomerProfilesTable extends Table
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
            'className' => 'Scid.Members'
        ]);
        $this->belongsTo('Profiles', [
            'foreignKey' => 'profile_id',
            'joinType' => 'INNER',
            'className' => 'Scid.Profiles'
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
        $rules->add($rules->existsIn(['profile_id'], 'Profiles'));

        return $rules;
    }
}
