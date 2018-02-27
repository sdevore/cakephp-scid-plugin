<?php
    /**
     * Created by PhpStorm.
     * User: sdevore
     * Date: 2/23/18
     * Time: 1:21 PM
     */

    namespace Scid\TestSuite;

    use Cake\TestSuite\IntegrationTestCase;

    trait ScidAuthTestTrait {

        /**
         * @param \Cake\TestSuite\IntegrationTestCase $object
         * @param array                               $user ['id'=>1,'username'=>'testing']
         * @return void
         */
        protected function authenticateUser (IntegrationTestCase $object, $user = []) {
            if (empty($user)) {
                // use a default user
                $user = ['id' => 1,
                            'username' => 'testing',
                            // other keys.
                        ];
            }
            $auth = [
                'Auth' => [
                    'User' => $user
                ]
            ];
            $object->session($auth);

        }
    }
