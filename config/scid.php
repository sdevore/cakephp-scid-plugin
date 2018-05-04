<?php
    /**
     * Created by PhpStorm.
     * User: sdevore
     * Date: 5/3/18
     * Time: 8:38 AM
     */
    $config = [
        'Scid' => [
            'MarkdownHelper' => ['parser' => 'markdown-extra'],
            'HtmlHelper'     => [
                'icons' => [
                    'email'      => 'envelope',
                    'send-email' => 'paper-plane',
                    'login'      => 'sign-in',
                    'logout'     => 'sign-out',
                    'profile'    => 'user-circle',
                    'edit'       => 'edit',
                    'delete'     => 'minus-square',
                    'view'       => 'eye',
                    'options'    => 'cogs',

                ],
            ],
        ],
    ];
    return $config;
