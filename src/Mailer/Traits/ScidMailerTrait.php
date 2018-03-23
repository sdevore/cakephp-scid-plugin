<?php
    /**
     * Created by PhpStorm.
     * User: sdevore
     * Date: 3/20/18
     * Time: 9:02 PM
     *
     */

    namespace Scid\Mailer\Traits;

    use App\Mailer;
    use Cake\Mailer\MailerAwareTrait;

    /**
     * Trait ScidMailerTrait
     * @var \Cake\Mailer\Mailer|\Cake\Mailer\MailerAwareTrait $this
     * @package Scid\Mailer\Traits
     */
    trait ScidMailerTrait
    {

        /**
         * @param $subject
         *
         * @return $this
         */
        public function setSubjectAndTitle($subject) {
            $this->setSubject($subject);
            $this->setViewVars(['title' => $subject]);
            return $this; // so it can be chained
        }

        /**
         * @param string $preview
         *
         * @return $this
         */
        public function setPreview($preview) {
            $this->setViewVars(['preview'=> $preview]);
            return $this; //so it can be chained
        }
    }
