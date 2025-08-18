<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Baskets".
 *
 * @property int $id
 * @property int $user_id
 * @property int $totalSum
 *
 * @property ProductBasket[] $productBaskets
 * @property Users $user
 */
class Baskets extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Baskets';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id'], 'required'],
            [['user_id'], 'integer'],
            [['user_id'], 'unique'],
            [['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::class, 'targetAttribute' => ['user_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'totalSum' => 'Total Sum',
        ];
    }

    /**
     * Gets query for [[ProductBaskets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductBaskets()
    {
        return $this->hasMany(ProductBasket::class, ['id_basket' => 'id']);
    }

    /**
     * Gets query for [[User]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(Users::class, ['id' => 'user_id']);
    }

}
