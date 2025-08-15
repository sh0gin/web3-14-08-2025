<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Orders".
 *
 * @property int $id
 * @property int $user_id
 * @property int $data_of_creation
 * @property int $general_price
 * @property string $track_code
 * @property int $status_id
 *
 * @property OrdersProducts[] $ordersProducts
 * @property ReasonCancellation[] $reasonCancellations
 * @property StatusOrders $status
 * @property Users $user
 */
class Orders extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Orders';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['user_id', 'data_of_creation', 'general_price', 'track_code', 'status_id'], 'required'],
            [['user_id', 'data_of_creation', 'general_price', 'status_id'], 'integer'],
            [['track_code'], 'string', 'max' => 255],
            [['status_id'], 'exist', 'skipOnError' => true, 'targetClass' => StatusOrders::class, 'targetAttribute' => ['status_id' => 'id']],
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
            'data_of_creation' => 'Data Of Creation',
            'general_price' => 'General Price',
            'track_code' => 'Track Code',
            'status_id' => 'Status ID',
        ];
    }

    /**
     * Gets query for [[OrdersProducts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrdersProducts()
    {
        return $this->hasMany(OrdersProducts::class, ['id_orders' => 'id']);
    }

    /**
     * Gets query for [[ReasonCancellations]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getReasonCancellations()
    {
        return $this->hasMany(ReasonCancellation::class, ['order_id' => 'id']);
    }

    /**
     * Gets query for [[Status]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getStatus()
    {
        return $this->hasOne(StatusOrders::class, ['id' => 'status_id']);
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
