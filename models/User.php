<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "Users".
 *
 * @property int $id
 * @property string $first_name
 * @property string $last_name
 * @property string $password
 * @property string $email
 * @property int $balance
 * @property string|null $token
 * @property int $role_id
 *
 * @property Baskets[] $baskets
 * @property Orders[] $orders
 * @property Role $role
 */
class User extends \yii\db\ActiveRecord implements IdentityInterface
{

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Users';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['password', 'first_name', 'last_name', 'email'], 'required', 'on' => 'register'],

            ['email', 'email'],
            ['email', 'unique', 'on' => 'register'],
            ['password', 'match', 'pattern' => '/(?=.*[a-z])(?=.*[0-9])[0-9a-zA-Z!@#$%^&*]{6}/'],
            
        ];
    }


    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::findOne(['token' => $token]);
    }

    public function getId()
    {
        // return $this->id;
    }

    public function getAuthKey()
    {
        // return $this->authKey;
    }

    public function validateAuthKey($authKey)
    {
        // return $this->authKey === $authKey;
    }

    public function validatePassword($password)
    {
        return Yii::$app->security->validatePassword($password, $this->password);
    }

    public function getRoleId($role)
    {
        return Role::findOne(['role', $role])->id;
    }

    public function isAdmin() {
        return $this->role_id == 2 ? true : false;
    }
}
