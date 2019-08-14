<?php
namespace Scid\Model\Entity;

use Cake\ORM\Entity;

/**
 * ScidCustomerProfile Entity
 *
 * @property string $id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $member_id
 * @property string $profile_id
 * @property string $email
 * @property string $config
 *
 * @property \App\Model\Entity\Member $member
 * @property \Scid\Model\Entity\Profile $profile
 * @property \Scid\Model\Entity\PaymentProfile[] $payment_profiles
 */
class CustomerProfile extends Entity
{

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
        'created' => true,
        'modified' => true,
        'member_id' => true,
        'profile_id' => true,
        'email' => true,
        'config' => true,
        'member' => true,
        'profile' => true
    ];
}
