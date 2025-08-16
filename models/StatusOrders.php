<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Status_orders".
 *
 * @property int $id
 * @property string $name
 *
 * @property Orders[] $orders
 */
class StatusOrders extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Status_orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name'], 'required'],
            [['name'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'name' => 'Name',
        ];
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Orders::class, ['status_id' => 'id']);
    }

    public static function getStatusName($id) {
        return self::findOne($id)->name;
    }

}
