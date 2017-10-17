<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'controllerNamespace' => 'console\controllers',
    'controllerMap' => [
        'fixture' => [
            'class' => 'yii\console\controllers\FixtureController',
            'namespace' => 'common\fixtures',
        ],
        //@see: http://www.yiiframework.com/doc-2.0/yii-console-controllers-migratecontroller.html
        'migrate' => [
            'class' => 'yii\console\controllers\MigrateController',
            //Use this for namespaced migrations
            'migrationNamespaces' => [
                //'console\migrations',
            ],
            //Use this for migrations without namespace
            'migrationPath' => [
                '@app/migrations',
                '@vendor/pheme/yii2-settings/migrations',
                '@yii/rbac/migrations'
            ],
        ],
    ],
    'components' => [
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
    ],
    'params' => $params,
];
