<?php
    /**
     * Created by PhpStorm.
     * User: sdevore
     * Date: 4/30/18
     * Time: 2:46 PM
     */

    use Cake\Core\Configure;
    use Cake\Core\Plugin;
    use Cake\Database\Type;
    Type::map('money', 'Scid\Database\Type\MoneyType');

    Configure::load('Scid.scid');
    collection((array)Configure::read('Scid.config'))->each(function ($file) {
        Configure::load($file);
    });

    Plugin::load('Robotusers/Excel');
    Plugin::load('CsvView');
