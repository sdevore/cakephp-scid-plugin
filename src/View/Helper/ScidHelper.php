<?php

    namespace Scid\View\Helper;

    use Cake\View\Helper;
    use Cake\View\View;
    use Money\Money;
    use Scid\Utility\ScidUtils;

    /**
     * Scid helper
     *
     * @property \Cake\View\Helper\HtmlHelper $Html
     */
    class ScidHelper extends Helper
    {

        public $helpers = ['Html'];

        /**
         * Default configuration.
         *
         * @var array
         */
        protected $_defaultConfig = [];

        /**
         * @param       $boolean
         * @param array $options
         */
        public function boolean($boolean, $options = ['type' => 'icon']) {
            if (empty($options['type'])) {
                $options['type'] = 'icon';
            }
            $true = 'true';
            $false = 'false';
            switch ($options['type']) {
                case 'icon':
                    $true = $this->Html->icon('check');
                    $false = '';
                    break;
                case 'checkbox':
                    $true = $this->Html->icon('check-square');
                    $false = $this->Html->icon('square');
                    break;
                case 'yes-no':
                    $true = 'Yes';
                    $false = 'No';
                    break;
            }

            return $boolean ? $true : $false;
        }

        /**
         * @param \Money\Money|NULL|\Scid\Database\I18n\Money $money
         *
         * @return string
         */
        public static function money($money = NULL) {
            return ScidUtils::formatMoney($money);
        }

        /**
         * format a phone number
         *
         * @param       $phone
         *
         * @return mixed
         */
        public
        function phone($phone) {
            $phone = preg_replace("/[^0-9]/", "", $phone);

            if (strlen($phone) == 7) {
                return preg_replace("/([0-9]{3})([0-9]{4})/", "$1-$2", $phone);
            }
            elseif (strlen($phone) == 10) {
                return preg_replace("/([0-9]{3})([0-9]{3})([0-9]{4})/", "($1) $2-$3", $phone);
            }
            else {
                return $phone;
            }
        }

        /**
         * use the Inflector to properly inflect if possible the string based on the count
         *
         * @param      $string
         * @param      $count
         * @param bool $includeCount
         *
         * @return mixed|null|string
         */
        public function plural($string, $count, $includeCount = TRUE) {
            return ScidUtils::plural($string, $count, $includeCount);
        }

        /**
         * @param   \Cake\I18n\Date|\Cake\I18n\Time    $dateTime
         * @param array $format
         *
         * @return string
         */
        public function dateTime($dateTime, $format = [
            \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT,
        ]) {
            return ScidUtils::dateTime($dateTime, $format);
        }

        /**
         * @param      \Cake\I18n\Date $date
         * @param array $format
         *
         * @return string
         */
        public function date($date, $format = [
            \IntlDateFormatter::SHORT, \IntlDateFormatter::NONE,
        ]) {
            return ScidUtils::dateTime($date, $format);
        }

        /**
         * @param     \Cake\I18n\Time  $time
         * @param array $format
         *
         * @return string
         */
        public function time($time, $format = [
            \IntlDateFormatter::NONE, \IntlDateFormatter::SHORT,
        ]) {
            return ScidUtils::dateTime($time, $format);
        }

    }
