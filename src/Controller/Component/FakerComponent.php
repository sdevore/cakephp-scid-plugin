<?php
namespace Scid\Controller\Component;

use Cake\Controller\Component;
use Cake\Controller\ComponentRegistry;
use Cake\Http\Client;
use Cake\ORM\Entity;
use Cake\Utility\Hash;

/**
 * Faker component
 */
class FakerComponent extends Component
{

    const SESSION_JSON_KEY = 'Scid.Faker.json';

    /**
     * Default configuration.
     *
     * @var array
     */
    protected $_defaultConfig = [
        'value_map' => [
            'first_name' => 'name.first',
            'last_name' => 'name.last',
            'name' => ['name.first','name.last'],
            'street'=>'location.street',
            'city'=>'location.city',
            'state'=>'location.state',
            'zip'=>'location.postcode',
            'email'=>'email',
            'username'=>'login.username',
            'password'=>'compass',
            'dob'=>'dob',
            'registered'=>'registered',
            'phone'=>'phone',
            'cell'=>'cell',
            'picture' =>'picture.large',
            'large-picture'=> 'picture.large',
            'medium-picture'=>'picture.medium',
            'thumbnail-picture'=>'picture.thumbnail'
        ]
    ];

    /*
     *
     */
    public function createFake($options = []) {
        $_options = ['format'=>'json',
                     'nat'=>'us'
        ];
        $query = $options + $_options;
        $http = new Client();
        $response = $http->get('https://api.randomuser.me', $query);
        $json = $response->json;
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
                    $value = Hash::format($json, $value_map , '%1 %2');
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

    public function value($key) {
        if ($this->getConfig('value_map.' . $key)) {
            $json = $this->__getJson();
            $value_map = $this->getConfig('value_map.' . $key);
            if (is_string($value_map)) {
                $value = Hash::extract($json, $value_map);
            }
            elseif (is_array($value_map)) {
                $value = Hash::format($json, $value_map, '%1 %2');
            }
            else {
                $value = '';
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
    private function __setJson($json) {
        $session = $this->getSession();
        $session->write(self::SESSION_JSON_KEY, $json);
    }

    /**
     * @return \Cake\Network\Session
     */
    private function getSession(): \Cake\Network\Session {
        return $this->_registry->getController()->request->session();
    }

    private function __getJson() {
        $session = $this->getSession();
       // $json = $session->read(self::SESSION_JSON_KEY);
        if (empty($json)) {
           $json =  $this->createFake();
        }
        return $json;
    }
}
