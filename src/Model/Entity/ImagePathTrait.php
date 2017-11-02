<?php
    /**
     * Created by PhpStorm.
     * User: sdevore
     * Date: 9/4/17
     * Time: 4:55 PM
     */

    namespace Scid\Model\Entity;

    use Cake\Core\Configure;
    use Cake\Utility\Inflector;
    use Cake\Routing\Router;

    /**
     * Trait ImagePathTrait
     *
     * @package Scid\Model\Entity
     */
    trait ImagePathTrait
    {

        /**
         * @param      $field
         * @param null $prefix
         * @param      $options
         *
         * @return string
         */
        public function getImagePath($field, $options = NULL) {
            $path = NULL;
            $prefix = '';
            if (!empty($options['prefix'])) {
                $prefix = $options['prefix'];
                unset($options['prefix']);
            }
            list($tableName, $dir, $pathPrefix, $fallback) = $this->_getPathComponents($field, $prefix);
            if (!empty($tableName) && !empty($dir)) {
                $path =
                    DS . 'files' . DS . $tableName . DS . $field . DS . $this->get($dir) . DS . $pathPrefix . $this->get($field);
                if (!file_exists(WWW_ROOT . $path) && !empty($fallback)) {
                    list($tableName, $dir, $pathPrefix, $newFallback) = $this->_getPathComponents($fallback, $prefix);
                    $path =
                        DS . 'files' . DS . $tableName . DS . $fallback . DS . $this->get($dir) . DS . $pathPrefix . $this->get($fallback);
                }
                if (!empty($options['full'])) {
                    $urlBase = Router::url('/', ['full' => TRUE]);
                }
                else {
                    $urlBase = '..';
                }
                $path = $urlBase . $path;
            }

            return $path;
        }

        /**
         * @param      $field
         * @param null $prefix
         * @param      $options
         *
         * @return string
         */
        public function getRetinaImagePath($field, $options = NULL) {
            $path = NULL;
            $prefix = '';
            if (!empty($options['prefix'])) {
                $prefix = $options['prefix'];
            }
            $prefix .= '-retina';
            list($tableName, $dir, $retinaPrefix, $fallback) = $this->_getPathComponents($field, $prefix);
            if (!empty($tableName) && !empty($dir) && !empty($retinaPrefix)) {
                $path =
                    DS . 'files' . DS . $tableName . DS . $field . DS . $this->get($dir) . DS . $retinaPrefix . $this->get($field);
                if (!file_exists(WWW_ROOT . $path) && !empty($fallback)) {
                    list($tableName, $dir, $retinaPrefix, $newFallback) = $this->_getPathComponents($fallback, $prefix);
                    $path =
                        DS . 'files' . DS . $tableName . DS . $fallback . DS . $this->get($dir) . DS . $retinaPrefix . $this->get($fallback);
                }
                if (!empty($options['full'])) {
                    $urlBase = Router::url('/', ['full' => TRUE]);
                }
                else {
                    $urlBase = '..';
                }
                $path = $urlBase . $path;
            }

            return $path;
        }

        /**
         * returns the class name of the entity without the namespace
         *
         * @return string
         */
        function getEntityName() {
            $object = $this;
            if (!is_object($object) && !is_string($object)) {
                return FALSE;
            }
            $class = explode('\\', (is_string($object) ? $object : get_class($object)));

            return $class[count($class) - 1];
        }


        /**
         * @param $field
         * @param $prefix
         *
         * @return array
         */
        protected function _getPathComponents($field, $prefix): array {
            $name = $this->getEntityName();
            $config = Configure::read('Proffer.' . Inflector::underscore($name) . '.' . $field);
            $tableName = NULL;
            $fallback = NULL;
            $dir = 'dir';
            if (!empty($config)) {
                $tableName = Inflector::tableize($name);
                if (!empty($config['dir'])) {
                    $dir = $config['dir'];
                }
                if (empty($prefix)) {
                    $prefix = '';
                }
                else {
                    if (empty($config['thumbnailSizes'][$prefix])) {
                        $prefix = '';
                    }
                    $prefix .= '_';
                }
            }
            if (!empty($config['fallback'])) {
                $fallback = $config['fallback'];
            }

            return [$tableName, $dir, $prefix, $fallback];
        }
    }
