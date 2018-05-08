<?php
    /**
     * Created by PhpStorm.
     * User: sdevore
     * Date: 5/8/18
     * Time: 11:30 AM
     */

    namespace Scid\Database\Type;

    use Cake\Database\Driver;
    use Cake\Database\Type;
    use Money\Currency;
    use Money\Money;
    use PDO;

    class MoneyType extends Type
    {

        public function toDatabase($value, Driver $driver) {
            if ($value === NULL) {
                $value = new Money(0, new Currency('USD'));
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
                return new Money($value, new Currency('USD'));
            } catch (\Exception $e) {
                return NULL;
            }
        }
    }
