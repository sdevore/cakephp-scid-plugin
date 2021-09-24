<?php
    /**
     * Created by PhpStorm.
     * User: sdevore
     * Date: 5/3/18
     * Time: 8:38 AM
     */
    $config = [
        'Scid' => [
            'viewDebug'      => FALSE,
            'MarkdownHelper' => ['parser' => 'markdown-extra'],
            'HtmlHelper'     => [
                'mime'  => [
                    'default'     => 'file',
                    'image'       => [
                        'default' => 'file-image',
                    ],
                    'application' => [
                        'default'      => 'file',
                        'msword'       => 'file-word',
                        'mspowerpoint' => 'file-powerpoint',
                        'pdf'          => 'file-pdf',
                        'excel'        => 'file-excel',
                        'audio'        => 'file-audio',
                        'video'        => 'file-video',
                        'zip'          => 'file-archive',
                    ],
                    'audio'       => ['default' => 'file-audio'],
                    'video'       => ['default' => 'file-video'],
                ],
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
