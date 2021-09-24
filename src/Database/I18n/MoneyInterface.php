<?php
    /**
     * Created by PhpStorm.
     * User: sdevore
     * Date: 5/13/18
     * Time: 8:23 PM
     */

    namespace Scid\Database\I18n;

    /**
     * Money Value Object.
     *
     * @author Mathias Verraes
     */
    interface MoneyInterface
    {

        /**
         * Checks whether a Money has the same Currency as this.
         *
         * @param Money $other
         *
         * @return bool
         */
        public function isSameCurrency(Money $other);

        /**
         * Checks whether the value represented by this object equals to the other.
         *
         * @param Money $other
         *
         * @return bool
         */
        public function equals(Money $other);

        /**
         * Returns an integer less than, equal to, or greater than zero
         * if the value of this object is considered to be respectively
         * less than, equal to, or greater than the other.
         *
         * @param Money $other
         *
         * @return int
         */
        public function compare(Money $other);

        /**
         * Checks whether the value represented by this object is greater than the other.
         *
         * @param Money $other
         *
         * @return bool
         */
        public function greaterThan(Money $other);

        /**
         * @param \Money\Money $other
         *
         * @return bool
         */
        public function greaterThanOrEqual(Money $other);

        /**
         * Checks whether the value represented by this object is less than the other.
         *
         * @param Money $other
         *
         * @return bool
         */
        public function lessThan(Money $other);

        /**
         * @param \Money\Money $other
         *
         * @return bool
         */
        public function lessThanOrEqual(Money $other);

        /**
         * Returns the value represented by this object.
         *
         * @return string
         */
        public function getAmount();

        /**
         * Returns the currency of this object.
         *
         * @return Currency
         */
        public function getCurrency();

        /**
         * Returns a new Money object that represents
         * the sum of this and an other Money object.
         *
         * @param Money $addend
         *
         * @return Money
         */
        public function add(Money $addend);

        /**
         * Returns a new Money object that represents
         * the difference of this and an other Money object.
         *
         * @param Money $subtrahend
         *
         * @return Money
         */
        public function subtract(Money $subtrahend);

        /**
         * Returns a new Money object that represents
         * the multiplied value by the given factor.
         *
         * @param float|int|string $multiplier
         * @param int              $roundingMode
         *
         * @return Money
         */
        public function multiply($multiplier, $roundingMode = self::ROUND_HALF_UP);

        /**
         * Returns a new Money object that represents
         * the divided value by the given factor.
         *
         * @param float|int|string $divisor
         * @param int              $roundingMode
         *
         * @return Money
         */
        public function divide($divisor, $roundingMode = self::ROUND_HALF_UP);

        /**
         * Returns a new Money object that represents
         * the remainder after dividing the value by
         * the given factor.
         *
         * @param Money $divisor
         *
         * @return Money
         */
        public function mod(Money $divisor);

        /**
         * Allocate the money according to a list of ratios.
         *
         * @param array $ratios
         *
         * @return Money[]
         */
        public function allocate(array $ratios);

        /**
         * Allocate the money among N targets.
         *
         * @param int $n
         *
         * @return Money[]
         *
         * @throws \InvalidArgumentException If number of targets is not an integer
         */
        public function allocateTo($n);

        /**
         * @param Money $money
         *
         * @return string
         */
        public function ratioOf(Money $money);

        /**
         * @return Money
         */
        public function absolute();

        /**
         * @return Money
         */
        public function negative();

        /**
         * Checks if the value represented by this object is zero.
         *
         * @return bool
         */
        public function isZero();

        /**
         * Checks if the value represented by this object is positive.
         *
         * @return bool
         */
        public function isPositive();

        /**
         * Checks if the value represented by this object is negative.
         *
         * @return bool
         */
        public function isNegative();

        /**
         * {@inheritdoc}
         *
         * @return array
         */
        public function jsonSerialize();
    }
