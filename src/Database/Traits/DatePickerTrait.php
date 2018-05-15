<?php
    /**
     * Created by PhpStorm.
     * User: sdevore
     * Date: 5/15/18
     * Time: 12:32 PM
     */

    namespace Scid\Database\Traits;

    use ArrayObject;
    use Cake\I18n\Date;
    use Cake\I18n\Time;

    trait DatePickerTrait
    {

        /**
         * @param ArrayObject  $data
         * @param       string $key
         * @param              $value
         * @param       array  $ranges          of the form
         *                                      $ranges = [
         *                                      'range'        => [
         *                                      'start' => 'startField',
         *                                      'end' => 'endField'],
         *                                      'range'        => [
         *                                      'start' => 'startField',
         *                                      'end' => 'endField'],
         *                                      'range'        => [
         *                                      'start' => 'startField',
         *                                      'end' => 'endField'],
         *                                      ...
         *                                      ];
         *
         * @return ArrayObject
         */
        private function marshalDuration(ArrayObject $data, $key, $value, $ranges) {
            if (in_array($key, array_keys($ranges))) {
                $range = $ranges[$key];
                if (strpos($value, '-') !== FALSE) {
                    list($start, $end) = explode('-', $value);
                    $start = new Time($start);
                    $end = new Time($end);
                    $data[$range['start']] = $start;
                    $data[$range['end']] = $end;
                }
            }

            return $data;
        }

        /**
         * @param \ArrayObject $data
         * @param              $modelKey
         * @param              $key
         * @param              $value
         *
         * @return \ArrayObject
         */
        private function marshalTime(ArrayObject $data, $modelKey, $key, $value) {
            if ($modelKey == $key) {
                if (is_string($value)) {
                    $data[$key] = new Time($value);
                }
            }

            return $data;
        }

        /**
         * @param \ArrayObject $data
         * @param              $modelKey
         * @param              $key
         * @param              $value
         *
         * @return \ArrayObject
         */
        private function marshalDate(ArrayObject $data, $modelKey, $key, $value) {
            if ($modelKey == $key) {
                if (is_string($value)) {
                    $data[$key] = new Date($value);
                }
            }

            return $data;
        }

        /**
         * @param $duration
         * @param $startField
         * @param $endField
         *
         * @return void
         */
        protected function setDuration($duration, $startField, $endField) {
            if (strpos($duration, '-') !== FALSE) {
                list($start, $end) = explode('-', $duration);
                $start = new Time($start);
                $end = new Time($end);

                $this->set($startField, $start);
                $this->set($endField, $end);
            }
        }

        /**
         * @param $startField
         * @param $endField
         * @param $formatArray
         *
         * @return null|string
         */
        protected function getDuration($startField, $endField, $formatArray = [
            \IntlDateFormatter::SHORT, \IntlDateFormatter::SHORT,
        ]) {
            /** @var \Cake\I18n\Date $start */
            $start = $this->get($startField);

            /** @var \Cake\I18n\Date $end */
            $end = $this->get($endField);
            if (!empty($start) && !empty($end)) {

                $return = $start
                        ->i18nFormat($formatArray)
                    . ' - ' .
                    $end
                        ->i18nFormat($formatArray);
            }
            else {
                $return = NULL;
            }

            return $return;
        }

        /**
         * @param $time
         *
         * @return \Cake\I18n\Time
         */
        protected function setTime($time) {
            if (is_string($time)) {
                $time = new Time($time);
            }

            return $time;
        }

        /**
         * @param $date
         *
         * @return \Cake\I18n\Date
         */
        protected function setDate($date) {
            if (is_string($date)) {
                $date = new Date($date);
            }

            return $date;
        }

        /**
         * @param Date $date
         *
         * @return string
         */
        protected function getDateTimePickerString($date, $format = [
            \IntlDateFormatter::SHORT,
            \IntlDateFormatter::SHORT,
        ]) {
            if (empty($date)) {
                $date = '';
            }
            else {
                $date = $date->i18nFormat(
                    $format);
            }

            return $date;
        }

        protected function getDatePickerString($date) {
            return $this->getDateTimePickerString($date, [
                \IntlDateFormatter::SHORT,
                \IntlDateFormatter::NONE,
            ]);
        }

        protected function getTimePickerString($date) {
            return $this->getDateTimePickerString($date, [
                \IntlDateFormatter::NONE,
                \IntlDateFormatter::SHORT,
            ]);
        }

    }
