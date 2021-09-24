<?php
    /**
     * Created by PhpStorm.
     * User: sdevore
     * Date: 5/8/18
     * Time: 11:35 AM
     */
namespace Scid\Model\Entity;

    use Money\Currencies\ISOCurrencies;
    use Money\Formatter\DecimalMoneyFormatter;
    use Money\Parser\IntlMoneyParser;

    trait MoneyEntityTrait
    {
        public function toDollars($cents) {
            return bcdiv((string)$cents, '100', 2);
        }

        protected function toCents($dollars) {
            return bcmul((string)$dollars, '100', 0);
        }

        /**
         * @param  \Scid\Database\I18n\Money|\Money\Money|string $amount
         *
         * @return string
         */
        public function cleanMoney($amount) {
            if ($amount instanceof \Scid\Database\I18n\Money) {
                $amount = $amount->money();
            }
            $currencies = new ISOCurrencies();
            if (is_string($amount)) {
                if (trim($amount) == '$') {
                    $amount = 0;
                }
                $numberFormatter = new \NumberFormatter('en_US', \NumberFormatter::CURRENCY);
                $moneyParser = new IntlMoneyParser($numberFormatter, $currencies);
                try {
                    $amount = $moneyParser->parse($amount);
                }
                catch (\Exception $e) {
                    $amount = trim(str_replace('$','',$amount));
                    return $amount;
                }
            }
            elseif(is_numeric($amount)) {
                return $amount;
            }


            $moneyFormatter = new DecimalMoneyFormatter($currencies);
            return $moneyFormatter->format($amount);
        }
    }
