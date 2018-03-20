<?php

    namespace Scid\View\Helper;

    use Cake\View\Helper;
    use Cake\View\View;
    use Cake\Http\Client;
    use Cake\ORM\Entity;
    use Cake\Utility\Hash;
    use Cake\Core\Configure;

    /**
     * Faker helper
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
    }
