<?php

namespace app\controllers;

use app\models\Role;
use app\models\User;
use Yii;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;

class UserController extends ActiveController
{

    public $modelClass = '';
    public $enableCsrfValidaion = '';

    public function behaviors()
    {
        $behaviors = parent::behaviors();

        // remove authentication filter
        $auth = $behaviors['authenticator'];
        unset($behaviors['authenticator']);

        // add CORS filter
        $behaviors['corsFilter'] = [
            'class' => \yii\filters\Cors::class,
            'cors' => [
                'Origin' => [isset($_SERVER['HTTP_ORIGIN']) ? $_SERVER['HTTP_ORIGIN'] : 'http://' . $_SERVER['REMOTE_ADDR']],
                'Access-Control-Request-Method' => ['GET', 'POST', 'PUT', 'PATCH', 'DELETE', 'HEAD', 'OPTIONS'],
                'Access-Control-Request-Headers' => ['*'],
            ],
            'actions' => [
                'logout' => [
                    'Access-Control-Allow-Credentials' => true,
                ],
                'get-user-info' => [
                    'Access-Control-Allow-Credentials' => true,
                ]
            ],
        ];


        $auth = [
            'class' => HttpBearerAuth::class,
            'only' => ['logout', 'get-user-info'],
        ];

        // re-add authentication filter
        $behaviors['authenticator'] = $auth;
        // avoid authentication on CORS-pre-flight requests (HTTP OPTIONS method)
        $behaviors['authenticator']['except'] = ['options'];

        return $behaviors;
    }

    public function actions()
    {
        $actions = parent::actions();

        // disable the "delete" and "create" actions
        unset($actions['delete'], $actions['create']);

        // customize the data provider preparation with the "prepareDataProvider()" method
        $actions['index']['prepareDataProvider'] = [$this, 'prepareDataProvider'];

        return $actions;
    }

    public function actionRegister()
    {
        $model = new User(['scenario' => 'register']);
        $model->load(Yii::$app->request->post(), '');
        if ($model->validate()) {
            $model->password = Yii::$app->security->generatePasswordHash($model->password);
            $model->save();
            return $this->asJson([
                'data' => [
                    'first_name' => $model->first_name,
                    'last_name' => $model->last_name,
                    'email' => $model->email,
                ]
            ]);
        } else {
            return $this->asJson([
                'error' => [
                    'code' => 422,
                    'message' => "Validation Erorr",
                    'error' => $model->errors,
                ]
            ]);
        }
    }

    public function actionLogin()
    {
        $post = Yii::$app->request->post();
        $model = new User();
        $model->load($post, '');


        if ($model->validate()) {
            $model = User::findOne(['email' => $post['email']]);
            if ($model && $model->validatePassword($post['password'])) {
                $model->token = Yii::$app->security->generateRandomString();
                $model->save(false);
                return $this->asJson([
                    'data' => [
                        'token' => $model->token,
                        'user' => [
                            'id' => $model->id,
                            'email' => $model->email,
                            'role' => Role::getRoleName($model->role_id),
                        ],
                        'code' => 200,
                        'message' => 'User login',
                    ]
                ]);
            } else {
                Yii::$app->response->statusCode = 401;
            }
        } else {
            return $this->asJson([
                'error' => [
                    'code' => 422,
                    'message' => "Validation Erorr",
                    'error' => $model->errors,
                ]
            ]);
        }
    }

    public function actionGetUserInfo()
    {
        $model = User::findOne(Yii::$app->user->identity->id);

        return $this->asJson([
            'data' => [
                'user' => [
                    'id' => $model->id,
                    'first_name' => $model->first_name,
                    'last_name' => $model->last_name,
                    'email' => $model->email,
                    'balance' => $model->balance,
                    'role' => Role::getRoleName($model->role_id),
                ]
            ]
        ]);
    }

    public function actionLogout() {
        $model = User::findOne(Yii::$app->user->identity->id);
        $model->token = null;
        $model->save(false);
        Yii::$app->response->statusCode = 201;
        return;
    }
}
