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
    use Cake\I18n\Date;
    use Cake\Core\Configure;
    use Cake\Core\Exception\Exception;
    use Cake\Filesystem\File;
    use Cake\I18n\FrozenDate;
    use Cake\Utility\Inflector;
    use Money\Currency;
    use Money\Money;
    use Money\Currencies\ISOCurrencies;
    use Money\Formatter\IntlMoneyFormatter;

    /**
     * Utility class
     */
    class ScidUtils
    {

        public static $states = [
            'AL' => "Alabama",
            'AK' => "Alaska",
            'AZ' => "Arizona",
            'AR' => "Arkansas",
            'CA' => "California",
            'CO' => "Colorado",
            'CT' => "Connecticut",
            'DE' => "Delaware",
            'DC' => "District of Columbia",
            'FL' => "Florida",
            'GA' => "Georgia",
            'HI' => "Hawaii",
            'ID' => "Idaho",
            'IL' => "Illinois",
            'IN' => "Indiana",
            'IA' => "Iowa",
            'KS' => "Kansas",
            'KY' => "Kentucky",
            'LA' => "Louisiana",
            'ME' => "Maine",
            'MD' => "Maryland",
            'MA' => "Massachusetts",
            'MI' => "Michigan",
            'MN' => "Minnesota",
            'MS' => "Mississippi",
            'MO' => "Missouri",
            'MT' => "Montana",
            'NE' => "Nebraska",
            'NV' => "Nevada",
            'NH' => "New Hampshire",
            'NJ' => "New Jersey",
            'NM' => "New Mexico",
            'NY' => "New York",
            'NC' => "North Carolina",
            'ND' => "North Dakota",
            'OH' => "Ohio",
            'OK' => "Oklahoma",
            'OR' => "Oregon",
            'PA' => "Pennsylvania",
            'RI' => "Rhode Island",
            'SC' => "South Carolina",
            'SD' => "South Dakota",
            'TN' => "Tennessee",
            'TX' => "Texas",
            'UT' => "Utah",
            'VT' => "Vermont",
            'VA' => "Virginia",
            'WA' => "Washington",
            'WV' => "West Virginia",
            'WI' => "Wisconsin",
            'WY' => "Wyoming",
        ];

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
        const DATE_ARRAY_FORMAT = 'Y-m-d';

        /**
         * @param      \Cake\I18n\Date|Chronos $start
         * @param      \Cake\I18n\Date|Chronos $end
         * @param bool $hierarchical
         *
         * @return array
         */
        public static function dateArray($start, $end, $hierarchical = false): array {
            $current = $start->copy();
            $result = [];

            while ($current->lte($end)) {
                if ($hierarchical) {
                    $result[$current->year][$current->month][$current->day] = ['date' => $current];
                }
                else {
                    $result[$current->format(self::DATE_ARRAY_FORMAT)] = ['date' => $current];
                }

                $current = $current->copy()->addDay(1);
            }

            return $result;
        }

        /**
         * @param array $dateArray
         *
         * @return array
         */
        public static function dateArrayToHeirarchy(array $dateArray) {
            $array = [];
            if (empty($dateArray)) {
                return $dateArray;
            }
            foreach ($dateArray as $key => $date) {
                list($year, $month, $day) = explode('-', $key);
                $array[$year][$month][$day] = $date;
                unset($dateArray[$key]);
            }
            return $array;
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
         * @param \Money\Money|\Scid\Database\I18n\Money|NULL $money
         *
         * @return string
         */
        public static function formatMoney($money = NULL) {
            if (NULL === $money) {
                $money = new \Scid\Database\I18n\Money(0, new Currency('USD'));
            }
            if ($money instanceof \Money\Money) {
                $money = new \Scid\Database\I18n\Money($money->getAmount(), $money->getCurrency());
            }

            return $money->format();
        }

        /**
         * @param      $string
         * @param      $count
         * @param bool $includeCount
         *
         * @throws \Aura\Intl\Exception
         * @return string
         */
        public static function plural($string, $count, $includeCount = TRUE) {
            if ($count != 1) {
                $pluralize = Inflector::pluralize($string);
            }
            else {
                $pluralize = $string;
            }
            if ($includeCount) {
                return __("{0} {1}", [$count, __n($string, $pluralize, $count)]);
            }
            else {
                return __("{0}", [__n($string, $pluralize, $count)]);
            }
        }

        /**
         * @param \Cake\Filesystem\File[]|\Cake\Filesystem\File|string|array $sources
         * @param   \Cake\Filesystem\File|string                             $destination path to destination file
         *
         * @return boolean|string
         */
        public static function concatinatePDF($sources, $destination) {
            $pdftkPath = Configure::read('Scid.PDF.path');
            if (!is_executable($pdftkPath)) {
                throw new Exception(sprintf('pdftk binary is not found or not executable: %s', $pdftkPath));
            }
            if (!is_array($sources)) {
                $sources = [$sources];
            }
            $paths = [];
            foreach ($sources as $source) {
                if ($source instanceof File) {
                    $paths[] = $source->path;
                }
                else {
                    $paths = $source;
                }
            }
            if ($destination instanceof File) {
                $destination = $destination->path;
            }
            $arguments = implode(' ', $paths);

            $command = sprintf('%s %s cat output %s', $pdftkPath, $arguments, $destination);

            $outputString = NULL;
            $output = 0;
            $out = exec($command, $outputString, $output);
            if ($output == 0) {
                return TRUE;
            }
            else {
                return FALSE;
            }
        }

        /**
         * @param  \Cake\I18n\Date|\Cake\I18n\Time $datetime
         * @param array                            $format
         *
         * @return string
         */
        public static function dateTime($datetime, $format = [
            \IntlDateFormatter::SHORT,
            \IntlDateFormatter::SHORT,
        ]) {
            if (empty($datetime)) {
                $datetime = '';
            }
            else {
                $datetime = $datetime->i18nFormat(
                    $format);
            }

            return $datetime;
        }

        /**
         * @param   \Cake\I18n\Date $date
         * @param array             $format
         *
         * @return string
         */
        public static function date($date, $format = [
            \IntlDateFormatter::SHORT,
            \IntlDateFormatter::NONE,
        ]) {
            return self::dateTime($date, $format);
        }

        /**
         * @param   \Cake\I18n\Time $time
         * @param array             $format
         *
         * @return string
         */
        public static function time($time, $format = [
            \IntlDateFormatter::NONE,
            \IntlDateFormatter::SHORT,
        ]) {
            return self::dateTime($time, $format);
        }
    }
