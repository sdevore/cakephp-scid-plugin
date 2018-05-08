<?php
    /**
     * Created by PhpStorm.
     * User: sdevore
     * Date: 5/8/18
     * Time: 12:53 PM
     */

    namespace Scid\I18n;

    use Money\Currencies\ISOCurrencies;
    use Money\Currency;
    use Money\Formatter\IntlMoneyFormatter;
use Money\Money as MoneyPHP;

    class Money
    {
        public static $defaultCurrency = 'USD';
        public static $defaultLocale = 'en_US';

        /**
         * @var MoneyPHP
         */
        protected  $_money;

        public function __construct($amount, $currency = 'USD', $locale = null)
        {
            if (!empty($locale)) {
                static::$defaultLocale = $locale;
            }
            $this->_money = new Money($amount, new Currency($currency));
        }

        /**
         * @param null $locale
         *
         * @return string
         */
        public function format($locale = null)
        {
            $locale = $locale ?: static::$defaultLocale;
            $currencies = new ISOCurrencies();

            $numberFormatter = new \NumberFormatter($locale, \NumberFormatter::CURRENCY);
            $moneyFormatter = new IntlMoneyFormatter($numberFormatter, $currencies);
           return $moneyFormatter->format($this->_money);
        }

        public function __toString()
        {
            return $this->format();
        }
    }
