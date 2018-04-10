<?php
    /**
     * Created by PhpStorm.
     * User: sdevore
     * Date: 4/10/18
     * Time: 10:58 AM
     */

    namespace Scid\Model\Table;

    use Cake\Filesystem\File;

    trait FilePondTrait
    {

        /**
         * @param $jsonString string to be decoded and used to build file array to match file uploads
         *
         * @return array|mixed
         */
        public function jsonDataToFileUploadArray($jsonString) {
            $jsonArray = json_decode($jsonString, TRUE);
            if (!empty($jsonArray) && is_array($jsonArray) && !empty($jsonArray['data'])) {
                $data = $jsonArray['data'];
                $data = base64_decode($data);
                if ($data) {
                    $file = new File(TMP . $jsonArray['id'], TRUE);
                    $result = $file->write($data);
                    if ($result) {
                        $jsonArray['tmp_name'] = $file->path;
                        $jsonArray['error'] = UPLOAD_ERR_OK;
                        unset($jsonArray['data']);
                    }
                    else {
                        $jsonArray['error'] = UPLOAD_ERR_CANT_WRITE;
                    }
                }
                else {
                    $jsonArray['error'] = UPLOAD_ERR_NO_FILE;
                }
            }
            return $jsonArray;
        }
    }
