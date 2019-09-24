<?php
/**
 * Created by PhpStorm.
 * User: web
 * Date: 15.04.19
 * Time: 18:57
 */

return [
    'class' => \yii\web\Application::class,
    'id' => 'test-console',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],

    'components' => [

        'db' => [
            'class' => \yii\db\Connection::class,
            'dsn' => 'mysql:host=127.0.0.1;dbname=testdb',
            'username' => 'web',
            'password' => '1web4',
            'charset' => 'utf8',
        ],
        'request' => [
            'enableCsrfValidation' => false,
        ],
        'log' => [
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'logFile' => '@runtime/logs/test.log',
                    'categories' => ['application'],
                    'levels' => ['error', 'trace', 'warning', 'info'],
                    'logVars' => [],
                ],
            ],
        ],
    ],
];