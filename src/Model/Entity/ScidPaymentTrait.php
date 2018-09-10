<?php
/**
 * Created by PhpStorm.
 * User: sdevore
 * Date: 9/4/18
 * Time: 6:43 PM
 *
 * @var \Cake\Datasource\EntityInterface    $this
 * @property string                         $credit_card_number
 * @property string                         $card_code
 * @property string                         $scid_state
 * @property string                         $scid_ref_id
 * @property   AnetAPI\LineItemType[]|array $lineItems
 */

namespace Scid\Model\Entity;

use ArrayObject;
use Cake\I18n\Date;
use Cake\I18n\FrozenDate;
use Cake\I18n\FrozenTime;
use Cake\I18n\Time;
use net\authorize\api\contract\v1 as AnetAPI;
use Scid\Database\I18n\Money;
use Tools\Utility\Text;

trait ScidPaymentTrait
{

    protected $_invoice_items = [];

    /**
     * @param   string           $itemId if invoice item is an enrolled course convention has course_id-student_id
     * @param   string           $name
     * @param  string            $description
     * @param float|string|Money $unitPrice
     * @param int                $quantity
     * @param bool               $taxable
     * @return bool
     */
    public function addInvoiceItem($itemId, $name, $description, $unitPrice, $quantity = 1, $taxable = FALSE) {
        if (sizeof($this->_invoice_items >= 25)) {
            return FALSE;
        }
        $lineItem = new AnetAPI\LineItemType();
        if (!empty($description)) {
            $lineItem->setDescription(Text::truncate($description, 200));
        }
        $lineItem->setItemId($itemId);
        $lineItem->setName(Text::truncate($name, 25));
        $unitPrice = $this->cleanMoney($unitPrice);
        $lineItem->setQuantity(1);
        $lineItem->setUnitPrice($unitPrice);
        $items[] = $lineItem;
        $this->_invoice_items[] = $lineItem;
        return TRUE;
    }

    /**
     * @return AnetAPI\LineItemType[]
     */
    protected function _getInvoiceItems() {

        return $this->_invoice_items;
    }



    public function setExpMonthYear($month, $year) {
        $this->expDate = __('{0}/{1}', $month, $year);
    }

    protected function _getExpirationDate() {
        if (!empty($this->_properties['expMonth']) && !empty($this->_properties['expYear'])) {
            $this->setExpMonthYear($this->_properties['expMonth'], $this->_properties['expYear']);
        }
        try {
            return FrozenDate::createFromFormat( 'm/Y',$this->expDate);
        } catch (Exception $e) {
            return NULL;
        }
    }
}
