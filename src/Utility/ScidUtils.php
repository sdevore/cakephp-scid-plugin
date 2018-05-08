<?php
    /**
     * Created by PhpStorm.
     * User: sdevore
     * Date: 4/6/17
     * Time: 2:09 PM
     */

    namespace Scid\Utility;

    use Cake\Chronos\Chronos;
    use Cake\Chronos\ChronosInterface;
    use Cake\Chronos\Date;
    use Money\Currency;
    use Money\Money;
    use Money\Currencies\ISOCurrencies;
    use Money\Formatter\IntlMoneyFormatter;

    /**
     * Utility class
     */
    class ScidUtils
    {

        /**
         * @var ISOCurrencies
         */
        public static $currencies;

        /**
         * @var \NumberFormatter
         */
        public static $numberFormatter;

        /**
         * @var IntlMoneyFormatter
         */
        public static $moneyFormatter;

        /**
         * returns an array with keys of Date::toDateString => ['date'=> Date]
         *
         * @param Date|Chronos $start
         * @param Date|Chronos $end
         *
         * @return Date|Chronos[]
         */
        public static function dateArray($start, $end): array {
            $current = $start;
            $result = [];

            while ($current->lte($end)) {
                $result[$current->toDateString()] = ['date' => $current];
                $current = $current->addDay();
            }

            return $result;
        }

        /**
         * returns an array with keys of Date::toDateString => ['date'=> Date]
         *
         * @param null $date
         * @param bool $includeOtherMonths
         *
         * @return array
         */
        public static function monthArray($date = NULL, $includeOtherMonths = TRUE) {
            if (empty($date)) {
                $date = Date::now();
            }
            elseif (!($date instanceof ChronosInterface)) {
                $date = new Date($date);
            }

            $first = $date->startOfMonth();
            $last = $date->endOfMonth();
            if ($includeOtherMonths) {
                $first = $first->startOfWeek();
                $last = $last->endOfWeek();
            }

            return self::dateArray($first, $last);
        }

        /**
         * @param \Money\Money|NULL $money
         *
         * @return string
         */
        public static function formatMoney(Money $money = NULL) {
            if (NULL === $money) {
                $money = new Money(0, new Currency('USD'));
            }
            if (empty(ScidUtils::$currencies)) {
                ScidUtils::$currencies = new ISOCurrencies();
            }
            if (empty(ScidUtils::$numberFormatter)) {
             ScidUtils::$numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
            }
            if (empty(ScidUtils::$moneyFormatter)) {
                ScidUtils::$moneyFormatter = new IntlMoneyFormatter(ScidUtils::$numberFormatter, ScidUtils::$currencies);
            }
            return ScidUtils::$moneyFormatter->format($money);

        }
    }
