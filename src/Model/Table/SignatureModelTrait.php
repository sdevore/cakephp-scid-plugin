<?php
    /**
     * Created by PhpStorm.
     * User: sdevore
     * Date: 4/10/18
     * Time: 10:58 AM
     */

    namespace Scid\Model\Table;

    use Cake\Filesystem\File;

    trait SignatureModelTrait
    {

        /**
         * @param $jsonString string to be decoded and used to build file array to match file uploads
         *
         * @return array|mixed
         */
        public function dataURIToFileUploadArray($data_uri) {
            list($label, $data) = explode(",", $data_uri);
            list($header, $types) = explode(':', $label);
            if (!empty($types)) {
                list($mime_type, $encoding) = explode(';', $types);
            }
            if (empty($mime_type)) {
                $mime_type = 'image/svg';
            }
            if (empty($encoding) || 'base64' == $encoding) {
                $data = base64_decode($data);
            }
            $array = [];
            if ($data) {
                $fileName = uniqid('signature') . '.svg';
                $file = new File(TMP . $fileName, TRUE);
                $result = $file->write($data);
                if ($result) {
                    $array['tmp_name'] = $file->path;
                    $array['error'] = UPLOAD_ERR_OK;
                    $array['type'] = $mime_type;
                    $array['name'] = $fileName;
                    $array['size'] = $file->size();
                }
                else {
                    $array['error'] = UPLOAD_ERR_CANT_WRITE;
                }
            }
            else {
                $array['error'] = UPLOAD_ERR_NO_FILE;
            }

            return $array;
        }
    }
