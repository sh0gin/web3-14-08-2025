<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
    ],
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'asdfasdf',
            'baseUrl' => '',
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
                'multipart/form-data' => 'yii\web\MultipartFormDataParser',
            ]
        ],
        'response' => [
            'format' => yii\web\Response::FORMAT_JSON,
            'charset' => 'UTF-8',
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                $response = $event->sender;
                if ($response->statusCode == 404) {
                    $response->data = [
                        'message' => 'not found',
                    ];
                }
                if ($response->statusCode == 403) {
                    $response->data = [
                        'message' => 'forbbiden for you',
                    ];
                }
                if ($response->statusCode == 401) {
                    $response->data = [
                        'message' => 'login failed',
                    ];
                }
            },
            'formatters' => [
                \yii\web\Response::FORMAT_JSON => [
                    'class' => 'yii\web\JsonResponseFormatter',
                    'prettyPrint' => YII_DEBUG, // use "pretty" output in debug mode
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                    // ...
                ],
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\User',
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => \yii\symfonymailer\Mailer::class,
            'viewPath' => '@app/mail',
            // send all mails to a file by default.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'enableStrictParsing' => true,
            'showScriptName' => false,
            'rules' => [
                ['class' => 'yii\rest\UrlRule', 'controller' => 'user'],
                "POST api/register" => "user/register",
                "OPTIONS api/register" => "option",

                "POST api/login" => "user/login",
                "OPTIONS api/register" => "option",

                "POST api/add-product" => "product/add-product",
                "OPTIONS api/add-product" => "option",

                "POST api/add-category" => "product/add-category",
                "OPTIONS api/add-category" => "option",

                "POST api/get-products" => "product/get-products",
                "OPTIONS api/get-products" => "option",

                "POST api/create-basket" => "product/create-basket",
                "OPTIONS api/create-basket" => "option",

                "POST api/add-one-product/<id>" => "product/add-one-product",
                "OPTIONS api/add-one-product/<id>" => "option",

                "POST api/del-one-product/<id>" => "product/del-one-product",
                "OPTIONS api/del-one-product/<id>" => "option",

                "POST api/del-all-products/<id>" => "product/del-all-product",
                "OPTIONS api/del-all-products/<id>" => "option",

                "POST api/sum-for-basket" => "product/sum-for-basket",
                "OPTIONS api/sum-for-basket" => "option",

                "POST api/order" => "product/order",
                "OPTIONS api/order" => "option",

                "POST api/cancel-orders/<id>" => "product/cancel-orders",
                "OPTIONS api/cancel-orders/<id>" => "option",

                "GET api/get-orders" => "product/get-orders",
                "OPTIONS api/get-orders" => "option",

                "GET api/get-user-info" => "user/get-user-info",
                "OPTIONS api/get-user-info" => "option",

                "GET api/logout" => "user/logout",
                "OPTIONS api/logout" => "option",
            ],
        ],
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        'allowedIPs' => ['*'],
    ];
}

return $config;
