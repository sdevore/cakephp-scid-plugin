<?php
    /**
     * Created by PhpStorm.
     * User: sdevore
     * Date: 5/8/18
     * Time: 11:30 AM
     */

    namespace App\Database\Type;

    use Cake\Database\Driver;
    use Cake\Database\Type;
    use Scid\I18n\Money;
    use PDO;

    class MoneyType extends Type
    {

        public function toDatabase($value, Driver $driver) {
            if ($value === NULL) {
                $value = 0;
            }

            return json_encode($this->_toMoney($value, Money::$defaultCurrency));
        }

        public function toPHP($value, Driver $driver) {
            if ($value === NULL) {
                return $value;
            }
            $value = json_decode($value, TRUE);

            return $this->_toMoney($value['amount'], $value['currency']);
        }

        public function marshal($value) {
            if ($value instanceof Money) {
                return $value;
            }
            try {
                return $this->_toMoney($value, Money::$defaultCurrency);
            } catch (\Exception $e) {
                return NULL;
            }
        }

        protected function _toMoney($value, $currency) {
            if (is_int($value)) {
                $value = new Money($value, $currency);
            }
            if (is_bool($value) || empty($value)) {
                return NULL;
            }
            if (is_string($value)) {
                $value = Money::fromString($value, $currency);
            }
            if (is_array($value)) {
                $value = $this->_toMoney($value['amount'], $value['currency']);
            }

            return $value;
        }
    }
