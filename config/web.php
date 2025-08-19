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
            'identityClass' => 'app\models\Users',
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
                "OPTIONS api/register" => "options",

                "POST api/login" => "user/login",
                "OPTIONS api/login" => "options",

                "POST api/products/add-product" => "product/add-product",
                "OPTIONS api/add-product" => "options",

                "POST api/category/add-category" => "product/add-category",
                "OPTIONS api/add-category" => "options",

                "GET api/products" => "product/get-products",
                "OPTIONS api/get-products" => "options",

                "POST api/products" => "product/get-products",
                "OPTIONS api/get-products" => "options",
                
                "POST api/product/add-one-product/<id>" => "product/add-one-product",
                "OPTIONS api/add-one-product/<id>" => "options",

                "DELETE api/product/del-one-product/<id>" => "product/del-one-product",
                "OPTIONS api/del-one-product/<id>" => "options",

                "DELETE api/product/del-all-products/<id>" => "product/del-all-product",
                "OPTIONS api/del-all-products/<id>" => "options",

                "POST api/order" => "product/order",
                "OPTIONS api/order" => "options",

                "POST api/cancel-orders/<id>" => "product/cancel-orders",
                "OPTIONS api/cancel-orders/<id>" => "options",

                "POST api/put-balance" => "product/put-balance",
                "OPTIONS api/cancel-orders/<id>" => "options",

                "GET api/orders/get-orders" => "product/get-orders",
                "OPTIONS api/get-orders" => "options",

                "GET api/get-user-info" => "user/get-user-info",
                "OPTIONS api/get-user-info" => "options",

                "GET api/logout" => "user/logout",
                "OPTIONS api/logout" => "options",

                "POST api/product/search" => "product/search-products",
                "OPTIONS api/search" => "options",

                "GET api/orders/<code>" => "product/get-info-orders",
                "OPTIONS api/orders/<code>" => "options",
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
