<?php
/**
 * Created by PhpStorm.
 * User: sdevore
 * Date: 9/4/18
 * Time: 6:43 PM
 *
 * @var \Cake\Datasource\EntityInterface $this
 * @property string                      $credit_card_number
 * @property string                      $card_code
 * @property string                      $scid_state
 * @property string                      $scid_ref_id
 */

namespace Scid\Entity;

use ArrayObject;
use Cake\I18n\Date;
use Cake\I18n\Time;

trait ScidPaymentTrait
{


    protected function _getCreditCardNumber() {
        if (!empty($this->_properties['credit_card_number'])) {
            return $this->_properties['credit_card_number'];
        } else {
            return NULL;
        }

    }

    protected function _setCreditCardNumber($value) {
        $this->_properties['credit_card_number'] = preg_replace('/\D+/', '', $value);
        $this->set('number', substr($this->_properties['credit_card_number'], -4));
        $this->setDirty('number');
    }

    protected function _getCardCode() {
        if (!empty($this->_properties['card_code'])) {
            return $this->_properties['card_code'];
        } else {
            return NULL;
        }
    }

    protected function _setCardCode($code) {
        $this->set('card_code', $code);
    }

    protected function _getExpirationDate() {
        try {
            $date = FrozenDate::parseDate($this->expDate, 'm/Y');
        } catch (Exception $e) {
            return NULL;
        }
    }
}