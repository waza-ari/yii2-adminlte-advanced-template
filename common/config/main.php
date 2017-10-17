<?php

require(__DIR__ . '/aliases.php');

return [
    'vendorPath' => dirname(dirname(__DIR__)) . '/vendor',
    'components' => [
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'settings' => [
            'class' => 'pheme\settings\components\Settings'
        ],
        'authManager' => [
            'class' => 'yii\rbac\DbManager',
        ],
        'i18n' => [
            'translations' => [
                '*' => [
                    'class' => 'yii\i18n\GettextMessageSource',
                    'basePath' => '@common/messages',
                ],
            ],
        ],
    ],
];
