<?php
    /**
     * Created by PhpStorm.
     * User: sdevore
     * Date: 5/8/18
     * Time: 12:53 PM
     */

    namespace Scid\Database\I18n;

    use Money\Currencies\ISOCurrencies;
    use Money\Currency;
    use Money\Formatter\IntlMoneyFormatter;
    use Money\Money as MoneyPHP;


    class Money
    {

        public static $defaultCurrency = 'USD';
        public static $defaultLocale = 'en_US';

        /**
         * @var \Money\Money
         */
        protected $_money;

        /**
         * Money constructor.
         *
         * @param   int|string|MoneyPHP     $amount
         * @param string $currency
         * @param null   $locale
         */
        public function __construct($amount, $currency = 'USD', $locale = NULL) {
            if ($amount instanceof MoneyPHP) {
                $this->_money = $amount;
            }
            else {
                if (!empty($locale)) {
                    static::$defaultLocale = $locale;
                }
                if (is_string($currency)) {
                    $currency = new Currency($currency);
                }

                $this->_money = new MoneyPHP($amount, $currency);
            }

        }

        /**
         * @param null $locale
         *
         * @return string
         */
        public function format($locale = NULL) {
            $locale = $locale ?: static::$defaultLocale;
            $currencies = new ISOCurrencies();

            $numberFormatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
            $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);

            return $moneyFormatter->format($this->_money);
        }

        public function __toString() {
            return $this->format();
        }

        /**
         * Checks whether a Money has the same Currency as this.
         *
         * @param Money $other
         *
         * @return bool
         */
        public function isSameCurrency(Money $other) {

            return $this->_money->isSameCurrency($other->money());
        }

        /**
         * Checks whether the value represented by this object equals to the other.
         *
         * @param Money $other
         *
         * @return bool
         */
        public function equals(Money $other) {
            return $this->_money->equals($other->money());
        }

        /**
         * Returns an integer less than, equal to, or greater than zero
         * if the value of this object is considered to be respectively
         * less than, equal to, or greater than the other.
         *
         * @param Money $other
         *
         * @return int
         */
        public function compare(Money $other) {
            return $this->_money->compare($other->money());
        }

        /**
         * Checks whether the value represented by this object is greater than the other.
         *
         * @param Money $other
         *
         * @return bool
         */
        public function greaterThan(Money $other) {
            return $this->_money->greaterThan($other->money());
        }

        /**
         * @param Money $other
         *
         * @return bool
         */
        public function greaterThanOrEqual(Money $other) {
            return $this->_money->greaterThanOrEqual($other->money());
        }

        /**
         * Checks whether the value represented by this object is less than the other.
         *
         * @param Money $other
         *
         * @return bool
         */
        public function lessThan(Money $other) {
            return $this->_money->lessThan($other->money());
        }

        /**
         * @param Money $other
         *
         * @return bool
         */
        public function lessThanOrEqual(Money $other) {
            return $this->_money->lessThanOrEqual($other->money());
        }

        /**
         * Returns the value represented by this object.
         *
         * @return string
         */
        public function getAmount() {
            return $this->_money->getAmount();
        }

        /**
         * Returns the value represented by this object.
         *
         * @return string
         */
        public function getDouble() {
            return $this->_money->getAmount()/100;
        }

        /**
         * Returns the currency of this object.
         *
         * @return Currency
         */
        public function getCurrency() {
            return $this->_money->getCurrency();
        }

        /**
         * Returns a new Money object that represents
         * the sum of this and an other Money object.
         *
         * @param Money $addend
         *
         * @return Money
         */
        public function add(Money $addend) {
            return new Money($this->_money->add($addend->money()));
        }

        /**
         * Returns a new Money object that represents
         * the difference of this and an other Money object.
         *
         * @param Money $subtrahend
         *
         * @return Money
         */
        public function subtract(Money $subtrahend) {
            return new Money($this->_money->subtract($subtrahend->money()));
        }

        /**
         * Returns a new Money object that represents
         * the multiplied value by the given factor.
         *
         * @param float|int|string $multiplier
         * @param int              $roundingMode
         *
         * @return Money
         */
        public function multiply($multiplier, $roundingMode = MoneyPHP::ROUND_HALF_UP) {
            return new Money($this->_money->multiply($multiplier, $roundingMode));
        }

        /**
         * Returns a new Money object that represents
         * the divided value by the given factor.
         *
         * @param float|int|string $divisor
         * @param int              $roundingMode
         *
         * @return Money
         */
        public function divide($divisor, $roundingMode = MoneyPHP::ROUND_HALF_UP) {
            return new Money($this->_money->divide($divisor, $roundingMode));
        }

        /**
         * Returns a new Money object that represents
         * the remainder after dividing the value by
         * the given factor.
         *
         * @param Money $divisor
         *
         * @return Money
         */
        public function mod(MoneyPHP $divisor) {
            return new Money($this->_money->mod($divisor));
        }

        /**
         * Allocate the money according to a list of ratios.
         *
         * @param array $ratios
         *
         * @return Money
         */
        public function allocate(array $ratios) {
            return new Money($this->_money->allocate($ratios));
        }

        /**
         * Allocate the money among N targets.
         *
         * @param int $n
         *
         * @return Money
         *
         * @throws \InvalidArgumentException If number of targets is not an integer
         */
        public function allocateTo($n) {
            return new Money($this->_money->allocateTo($n));
        }

        /**
         * @param Money $money
         *
         * @return string
         */
        public function ratioOf(Money $money) {
            return $this->_money->ratioOf($money->money());
        }

        /**
         * @return Money
         */
        public function absolute() {
            return new Money($this->_money->absolute());
        }

        /**
         * @return Money
         */
        public function negative() {
            return new Money($this->_money->negative());
        }

        /**
         * Checks if the value represented by this object is zero.
         *
         * @return bool
         */
        public function isZero() {
            return $this->_money->isZero();
        }

        /**
         * Checks if the value represented by this object is positive.
         *
         * @return bool
         */
        public function isPositive() {
            return $this->_money->isPositive();
        }

        /**
         * Checks if the value represented by this object is negative.
         *
         * @return bool
         */
        public function isNegative() {
            return $this->_money->isNegative();
        }

        /**
         * {@inheritdoc}
         *
         * @return array
         */
        public function jsonSerialize() {
            return $this->_money->jsonSerialize();
        }

        /**
         * @return \Money\Money
         */
        public function money() {
            return $this->_money;
        }

        /**
         * @param MoneyPHP $first
         * @param Money    ...$collection
         *
         * @return Money
         */
        public static function min(self $first, self ...$collection)
        {
            $min = $first;

            foreach ($collection as $money) {
                if ($money->lessThan($min)) {
                    $min = $money;
                }
            }

            return $min;
        }

        /**
         * @param Money $first
         * @param Money ...$collection
         *
         * @return Money
         */
        public static function max(self $first, self ...$collection)
        {
            $max = $first;

            foreach ($collection as $money) {
                if ($money->greaterThan($max)) {
                    $max = $money;
                }
            }

            return $max;
        }

        /**
         * @param Money $first
         * @param Money ...$collection
         *
         * @return Money
         */
        public static function sum(self $first, self ...$collection)
        {
            return $first->add(...$collection);
        }

        /**
         * @param Money $first
         * @param Money ...$collection
         *
         * @return Money
         */
        public static function avg(self $first, self ...$collection)
        {
            return $first->add(...$collection)->divide(func_num_args());
        }
    }
