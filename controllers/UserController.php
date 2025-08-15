<?php

namespace app\controllers;

use app\models\Role;
use yii\rest\ActiveController;

class UserController extends ActiveController
{

    public $modelClass = '';
    public $enableCsrfValidaion = '';




    public function getRoleId($role) {
        return Role::findOne(['role', $role])->id;
    }
}
