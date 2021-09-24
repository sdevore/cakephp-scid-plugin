<?php
    /**
     * Created by PhpStorm.
     * User: sdevore
     * Date: 5/8/18
     * Time: 11:30 AM
     */

    namespace Scid\Database\Type;

    use Scid\Model\Entity\MoneyEntityTrait;
    use Cake\Database\Driver;
    use Cake\Database\Type;
    use Money\Currency;
    use Scid\Database\I18n\Money;

    class MoneyType extends Type
    {
        use MoneyEntityTrait;

        public function toDatabase($value, Driver $driver) {
            if ($value === NULL) {
                $value = new Money(0, new Currency('USD'));
            }
            if (is_string($value)) {
                $value = $this->marshal($value);
            }
            return $value->getAmount();
        }

        public function toPHP($value, Driver $driver) {
            if ($value === NULL) {
                return $value;
            }
            return new Money($value, new Currency('USD'));
        }

        public function marshal($value) {
            if ($value instanceof Money) {
                return $value;
            }
            try {
                if (empty($value)) {
                    $value = 0;
                }
                if (is_string($value)) {
                    $value = $this->cleanMoney($value);
                }
                return new Money($value *100, new Currency('USD'));
            } catch (\Exception $e) {
                return NULL;
            }
        }
    }
