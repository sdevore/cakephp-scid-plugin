<?php
    /**
     * Created by PhpStorm.
     * User: sdevore
     * Date: 4/30/18
     * Time: 2:46 PM
     */

    use Cake\Core\Configure;

    Configure::load('Scid.scid');
    collection((array)Configure::read('Scid.config'))->each(function ($file) {
        Configure::load($file);
    });
