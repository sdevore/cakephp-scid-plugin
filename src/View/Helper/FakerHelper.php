<?php

    namespace Scid\View\Helper;

    use Cake\View\Helper;
    use Cake\View\View;
    use Cake\Http\Client;
    use Cake\ORM\Entity;
    use Cake\Utility\Hash;
    use Cake\Core\Configure;
    use Faker\Factory as FakerFactory;

    /**
     * @property string $name
     * @method string name(string $gender = null)
     * @property string $firstName
     * @method string firstName(string $gender = null)
     * @property string $firstNameMale
     * @property string $firstNameFemale
     * @property string $lastName
     * @property string $title
     * @method string title(string $gender = null)
     * @property string $titleMale
     * @property string $titleFemale
     *
     * @property string $citySuffix
     * @property string $streetSuffix
     * @property string $buildingNumber
     * @property string $city
     * @property string $streetName
     * @property string $streetAddress
     * @property string $postcode
     * @property string $address
     * @property string $country
     * @property float  $latitude
     * @property float  $longitude
     *
     * @property string $ean13
     * @property string $ean8
     * @property string $isbn13
     * @property string $isbn10
     *
     * @property string $phoneNumber
     *
     * @property string $company
     * @property string $companySuffix
     * @property string $jobTitle
     *
     * @property string $creditCardType
     * @property string $creditCardNumber
     * @method string creditCardNumber($type = null, $formatted = false, $separator = '-')
     * @property \DateTime $creditCardExpirationDate
     * @property string $creditCardExpirationDateString
     * @property array $creditCardDetails
     * @property string $bankAccountNumber
     * @method string iban($countryCode = null, $prefix = '', $length = null)
     * @property string $swiftBicNumber
     * @property string $vat
     *
     * @property string $word
     * @property string|array $words
     * @method string|array words($nb = 3, $asText = false)
     * @property string $sentence
     * @method string sentence($nbWords = 6, $variableNbWords = true)
     * @property string|array $sentences
     * @method string|array sentences($nb = 3, $asText = false)
     * @property string $paragraph
     * @method string paragraph($nbSentences = 3, $variableNbSentences = true)
     * @property string|array $paragraphs
     * @method string|array paragraphs($nb = 3, $asText = false)
     * @property string $text
     * @method string text($maxNbChars = 200)
     *
     * @method string realText($maxNbChars = 200, $indexSize = 2)
     *
     * @property string $email
     * @property string $safeEmail
     * @property string $freeEmail
     * @property string $companyEmail
     * @property string $freeEmailDomain
     * @property string $safeEmailDomain
     * @property string $userName
     * @property string $password
     * @method string password($minLength = 6, $maxLength = 20)
     * @property string $domainName
     * @property string $domainWord
     * @property string $tld
     * @property string $url
     * @property string $slug
     * @method string slug($nbWords = 6, $variableNbWords = true)
     * @property string $ipv4
     * @property string $ipv6
     * @property string $localIpv4
     * @property string $macAddress
     *
     * @property int       $unixTime
     * @property \DateTime $dateTime
     * @property \DateTime $dateTimeAD
     * @property string    $iso8601
     * @property \DateTime $dateTimeThisCentury
     * @property \DateTime $dateTimeThisDecade
     * @property \DateTime $dateTimeThisYear
     * @property \DateTime $dateTimeThisMonth
     * @property string    $amPm
     * @property int       $dayOfMonth
     * @property int       $dayOfWeek
     * @property int       $month
     * @property string    $monthName
     * @property int       $year
     * @property int       $century
     * @property string    $timezone
     * @method string amPm($max = 'now')
     * @method string date($format = 'Y-m-d', $max = 'now')
     * @method string dayOfMonth($max = 'now')
     * @method string dayOfWeek($max = 'now')
     * @method string iso8601($max = 'now')
     * @method string month($max = 'now')
     * @method string monthName($max = 'now')
     * @method string time($format = 'H:i:s', $max = 'now')
     * @method string unixTime($max = 'now')
     * @method string year($max = 'now')
     * @method \DateTime dateTime($max = 'now', $timezone = null)
     * @method \DateTime dateTimeAd($max = 'now', $timezone = null)
     * @method \DateTime dateTimeBetween($startDate = '-30 years', $endDate = 'now')
     * @method \DateTime dateTimeInInterval($date = '-30 years', $interval = '+5 days', $timezone = null)
     * @method \DateTime dateTimeThisCentury($max = 'now', $timezone = null)
     * @method \DateTime dateTimeThisDecade($max = 'now', $timezone = null)
     * @method \DateTime dateTimeThisYear($max = 'now', $timezone = null)
     * @method \DateTime dateTimeThisMonth($max = 'now', $timezone = null)
     *
     * @property string $md5
     * @property string $sha1
     * @property string $sha256
     * @property string $locale
     * @property string $countryCode
     * @property string $countryISOAlpha3
     * @property string $languageCode
     * @property string $currencyCode
     * @property boolean $boolean
     * @method boolean boolean($chanceOfGettingTrue = 50)
     *
     * @property int    $randomDigit
     * @property int    $randomDigitNotNull
     * @property string $randomLetter
     * @property string $randomAscii
     * @method int randomNumber($nbDigits = null, $strict = false)
     * @method int|string|null randomKey(array $array = array())
     * @method int numberBetween($min = 0, $max = 2147483647)
     * @method float randomFloat($nbMaxDecimals = null, $min = 0, $max = null)
     * @method mixed randomElement(array $array = array('a', 'b', 'c'))
     * @method array randomElements(array $array = array('a', 'b', 'c'), $count = 1, $allowDuplicates = false)
     * @method array|string shuffle($arg = '')
     * @method array shuffleArray(array $array = array())
     * @method string shuffleString($string = '', $encoding = 'UTF-8')
     * @method string numerify($string = '###')
     * @method string lexify($string = '????')
     * @method string bothify($string = '## ??')
     * @method string asciify($string = '****')
     * @method string regexify($regex = '')
     * @method string toLower($string = '')
     * @method string toUpper($string = '')
     * @method Generator optional($weight = 0.5, $default = null)
     * @method Generator unique($reset = false, $maxRetries = 10000)
     * @method Generator valid($validator = null, $maxRetries = 10000)
     *
     * @method integer biasedNumberBetween($min = 0, $max = 100, $function = 'sqrt')
     *
     * @property string $macProcessor
     * @property string $linuxProcessor
     * @property string $userAgent
     * @property string $chrome
     * @property string $firefox
     * @property string $safari
     * @property string $opera
     * @property string $internetExplorer
     * @property string $windowsPlatformToken
     * @property string $macPlatformToken
     * @property string $linuxPlatformToken
     *
     * @property string $uuid
     *
     * @property string $mimeType
     * @property string $fileExtension
     * @method string file($sourceDirectory = '/tmp', $targetDirectory = '/tmp', $fullPath = true)
     *
     * @method string imageUrl($width = 640, $height = 480, $category = null, $randomize = true, $word = null, $gray = false)
     * @method string image($dir = null, $width = 640, $height = 480, $category = null, $fullPath = true, $randomize = true, $word = null)
     *
     * @property string $hexColor
     * @property string $safeHexColor
     * @property string $rgbColor
     * @property array $rgbColorAsArray
     * @property string $rgbCssColor
     * @property string $safeColorName
     * @property string $colorName
     *
     * @method string randomHtml($maxDepth = 4, $maxWidth = 4)
     *
     */
    class FakerHelper extends Helper
    {

        const SESSION_JSON_KEY = 'Scid.Faker.json';
        /**
         * Default configuration.
         *
         * @var array
         */
        protected $_defaultConfig = [
            'value_map' => [
                'first_name'        => 'name.first',
                'last_name'         => 'name.last',
                'name'              => ['name.first', 'name.last'],
                'street'            => 'location.street',
                'city'              => 'location.city',
                'state'             => 'location.state',
                'zip'               => 'location.postcode',
                'email'             => 'email',
                'username'          => 'login.username',
                'password'          => 'compass',
                'dob'               => 'dob',
                'registered'        => 'registered',
                'phone'             => 'phone',
                'cell'              => 'cell',
                'picture'           => 'picture.large',
                'large-picture'     => 'picture.large',
                'medium-picture'    => 'picture.medium',
                'thumbnail-picture' => 'picture.thumbnail',
            ],
        ];

        /**
         * Default configuration.
         *
         * @var FakerFactory
         */
        protected $_faker;


        public function __construct(\Cake\View\View $View, array $config = []) {
            parent::__construct($View, $config);

            $this->_faker = FakerFactory::create();
        }

        function get_content($url)
        {
            $ch = curl_init();

            curl_setopt ($ch, CURLOPT_URL, $url);
            curl_setopt ($ch, CURLOPT_HEADER, 0);

            ob_start();

            curl_exec ($ch);
            curl_close ($ch);
            $string = ob_get_contents();

            ob_end_clean();

            return $string;
        }


        /**
         * @param array $options
         *
         * @return mixed
         */
        public function createFake($options = []) {
            $_options = [
                'format' => 'json',
                'nat'    => 'us',
            ];
            $query = $options + $_options;
            $arg = [];
            foreach ($query as $key=>$value) {
                $arg[] = urlencode($key) . '='. urlencode($value);
            }
            $http = new Client();
            try {
                $response = $http->get('https://api.randomuser.me', $query,['timeout'=>5]);
                $json = $response->json;
            }
            catch (Exception $e) {
                $response = $this->get_content('https://api.randomuser.me?' . join('&' , $arg));
                $json = json_decode($response, true);
            }

            $json = $json['results'][0];
            $this->__setJson($json);

            return $json;
        }

        /**
         * @param Entity $entity
         * @return void
         */
        public function populate($entity) {
            $visibleProperties = $entity->visibleProperties();
            $array = $entity->toArray();
            $json = $this->__getJson();
            foreach ($visibleProperties as $visibleProperty) {
                if ($this->getConfig('value_map.' . $visibleProperty)) {
                    $value_map = $this->getConfig('value_map.' . $visibleProperty);
                    if (is_string($value_map)) {
                        $value = $json[$value_map];
                    }
                    elseif (is_array($value_map)) {
                        $value = Hash::format($json, $value_map, '%1 %2');
                    }
                    else {
                        $value = '';
                    }
                    $value = ucwords($value);
                    $entity->set($visibleProperty, $value);
                }
            }

            return $entity;
        }

        /**
         * @param      $key
         * @param bool $reset
         *
         * @return array|mixed|null|string
         */
        public function value($key, $reset = FALSE) {
            if (Configure::read('debug')) {
                if ($this->getConfig('value_map.' . $key)) {
                    $value_map = $this->getConfig('value_map.' . $key);
                }
                else {
                    $value_map = $key;
                }
                $json = $this->__getJson($reset);

                if (is_string($value_map)) {
                    $value = Hash::extract($json, $value_map);
                }
                elseif (is_array($value_map)) {
                    $value = Hash::format($json, $value_map, '%1$s %2$s');
                }
                else {
                    $value = '';
                }
                if (is_array($value)) {
                    $value = $value[0];
                }
                $value = ucwords($value);

                return $value;
            }

            return '';
        }

        /**
         * @param $json
         * @return void
         */
        private
        function __setJson($json) {
            $session = $this->__getSession();
            $session->write(self::SESSION_JSON_KEY, $json);
        }

        /**
         * @return \Cake\Network\Session
         */
        private
        function __getSession(): \Cake\Network\Session {
            return $this->_View->request->getSession();
        }

        /**
         * @param bool $reset
         *
         * @return mixed|null|string
         */
        private
        function __getJson($reset = FALSE) {
            $session = $this->__getSession();
            $json = NULL;
            if (!$reset) {
                $json = $session->read(self::SESSION_JSON_KEY);
            }
            if (empty($json)) {
                $json = $this->createFake();
            }

            return $json;
        }

        /**
         * @param string $attribute
         *
         * @return mixed
         */
        public function __get($attribute)
        {
            return $this->_faker->__get($attribute);
        }

        /**
         * @param string $method
         * @param array $attributes
         *
         * @return mixed
         */
        public function __call($method, $attributes)
        {
            return $this->_faker->__call($method, $attributes);
        }
    }
