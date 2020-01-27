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
use Cake\Collection\Collection;
use Cake\I18n\Date;
use Cake\Core\Configure;
use Cake\Core\Exception\Exception;
use Cake\Filesystem\File;
use Cake\I18n\FrozenDate;
use Cake\Utility\Inflector;
use IntlDateFormatter;
use Money\Currency;
use Money\Money;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\IntlMoneyFormatter;
use mikehaertl\pdftk\Pdf;

/**
 * Utility class
 */
class ScidUtils
{

    /**
     * returns an array with keys of Date::toDateString => ['date'=> Date]
     *
     * @param Date|Chronos $start
     * @param Date|Chronos $end
     *
     * @return Date|Chronos[]
     */
    const DATE_ARRAY_FORMAT = 'Y-m-d';
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
        } else if (!($date instanceof ChronosInterface)) {
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
     * @param      \Cake\I18n\Date|FrozenDate|Chronos $start
     * @param      \Cake\I18n\Date|FrozenDate|Chronos $end
     * @param bool                                    $hierarchical
     *
     * @return array
     */
    public static function dateArray($start, $end, $hierarchical = FALSE): array {
        $current = $start->copy();
        $result = [];

        while ($current->lte($end)) {
            if ($hierarchical) {
                $result[$current->year][$current->month][$current->day] = ['date' => $current];
            } else {
                $result[$current->format(self::DATE_ARRAY_FORMAT)] = ['date' => $current];
            }

            $current = $current->copy()->addDay(1);
        }

        return $result;
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
        if ($money instanceof Money) {
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
        } else {
            $pluralize = $string;
        }
        if ($includeCount) {
            return __("{0} {1}", [$count, __n($string, $pluralize, $count)]);
        } else {
            return __("{0}", [__n($string, $pluralize, $count)]);
        }
    }

    /**
     * given an associted array of numerically keyed values retunr an array of range values
     *
     * @param array  $source
     * @param string $separator default '-'
     *
     * @return array
     */
    static function getKeyedRanges($source, $separator = '-') {
        if ($source instanceof Collection) {
            $source = $source->toArray();
        }
        $keys = array_keys($source);
        $ranges = ScidUtils::getRanges($keys);
        $result = [];
        foreach ($ranges as $range) {
            if (count($range) == 1)
                $result[] = $source[$range[0]];
            else {

                $result[] = $source[$range[0]] . $separator . $source[$range[count($range) - 1]];
            }
        }
        return $result;
    }

    /**
     * given an array of numbers group them by ranges
     *
     * @param array $aNumbers
     *
     * @return array
     */
    static function getRanges($aNumbers) {
        $aNumbers = array_unique($aNumbers);
        sort($aNumbers);
        $aGroups = [];
        for ($i = 0; $i < count($aNumbers); $i++) {
            if ($i > 0 && ($aNumbers[$i - 1] == $aNumbers[$i] - 1))
                array_push($aGroups[count($aGroups) - 1], $aNumbers[$i]);
            else
                array_push($aGroups, [$aNumbers[$i]]);
        }
        return $aGroups;
    }

    /**
     * @param \Cake\Filesystem\File[]|\Cake\Filesystem\File|string|array $sources
     * @param   \Cake\Filesystem\File|string                             $destination path to destination file
     *
     * @return boolean|string
     */
    public static function concatinatePDF($sources, $destination) {
        $handle = 'A';
// don't use the path directly anymore try using the plugin works a bit better
//        $pdftkPath = Configure::read('Scid.PDF.path');
//        if (!is_executable($pdftkPath)) {
//            throw new Exception(sprintf('pdftk binary is not found or not executable: %s', $pdftkPath));
//        }
        if (!is_array($sources)) {
            $sources = [$sources];
        }
        $paths = [];
        $pages = [];
        foreach ($sources as $source) {
            if ($source instanceof File) {
                $paths[] = $source->path;
                $pages[$handle++] = $source->path;
            } else {
                $paths[] = $source;
                $pages[$handle++] = $source;
            }
        }
        if ($destination instanceof File) {
            $destination = $destination->path;
        }
        // don't directly use the hard path and directly interact with the pdftk exec
//        $arguments = implode(' ', $paths);
//
//        $command = sprintf('%s %s cat output %s', $pdftkPath, $arguments, $destination);
//
//        $outputString = NULL;
//        $output = 0;
//        $out = exec($command, $outputString, $output);
        $bulkPDF = new PDF($pages);
        if (!$bulkPDF->saveAs($destination)) {
            $error = $bulkPDF->getError();
            return false;
        }
        else {
            return true;
        }
//        if ($output == 0) {
//            return TRUE;
//        } else {
//            return FALSE;
//        }
    }

    /**
     * @param   \Cake\I18n\Date $date
     * @param array             $format
     *
     * @return string
     */
    public static function date($date, $format = [
        IntlDateFormatter::SHORT,
        IntlDateFormatter::NONE,
    ]) {
        return self::dateTime($date, $format);
    }

    /**
     * @param  \Cake\I18n\Date|\Cake\I18n\Time $datetime
     * @param array                            $format
     *
     * @return string
     */
    public static function dateTime($datetime, $format = [
        IntlDateFormatter::SHORT,
        IntlDateFormatter::SHORT,
    ]) {
        if (empty($datetime)) {
            $datetime = '';
        } else {
            $datetime = $datetime->i18nFormat(
                $format);
        }

        return $datetime;
    }

    public
    static function phone($phone) {
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
     * @param   \Cake\I18n\Time $time
     * @param array             $format
     *
     * @return string
     */
    public static function time($time, $format = [
        IntlDateFormatter::NONE,
        IntlDateFormatter::SHORT,
    ]) {
        return self::dateTime($time, $format);
    }

    /**
     * @param    array    $data
     * @param string $monthField
     * @param string $yearField
     *
     * @return \Cake\I18n\FrozenDate
     */
    static function ccExpirationDate($data, $monthField = 'expMonth', $yearField = 'expYear') {
        $date = new FrozenDate();
        return $date->setDate($data[$yearField], $data[$monthField], 1);
    }

    /**
     * @param       $boolean
     * @param array $options
     */
    public static function boolean($boolean, $options = ['type' => 'yes-no']) {
        if (empty($options['type'])) {
            $options['type'] = 'check';
        }
        $true = 'true';
        $false = 'false';
        switch ($options['type']) {
            case 'check':
                $true = '&#10004;';
                $false = '';
                break;
            case 'checkbox':
                $true = '&#9745;';
                $false = '&#9744;';
                break;
            case 'yes-no':
                $true = 'Yes';
                $false = 'No';
                break;
            case 'array':
                $true = $options['true'];
                $false = $options['false'];
        }

        return $boolean ? $true : $false;
    }
}
