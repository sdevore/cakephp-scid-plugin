<?php
namespace Scid\Model\Entity;

use Cake\ORM\Entity;

/**
 * ScidPaymentProfile Entity
 *
 * @property string $id
 * @property \Cake\I18n\FrozenTime $created
 * @property \Cake\I18n\FrozenTime $modified
 * @property int $member_id
 * @property string $customer_profile_id
 * @property string $payment_profile_id
 * @property bool $primary
 * @property string $card_number
 * @property string $expiration_date
 * @property string $card_type
 *
 * @property \Cake\ORM\Entity $member
 * @property \Scid\Model\Entity\CustomerProfile $customer_profile
 * @property \Scid\Model\Entity\PaymentProfile $payment_profile
 */
class PaymentProfile extends Entity
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
        'customer_profile_id' => true,
        'payment_profile_id' => true,
        'primary' => true,
        'card_number' => true,
        'expiration_date' => true,
        'card_type' => true,
        'member' => true,
        'customer_profile' => true,
        'payment_profile' => true
    ];
}
