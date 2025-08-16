<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "OrdersProducts".
 *
 * @property int $id
 * @property int $products_id
 * @property int $orders_idi
 * @property int $count
 * @property int $totalCount
 *
 * @property Orders $orders
 * @property Products $products
 */
class OrdersProducts extends \yii\db\ActiveRecord
{
    public $data_of_creation;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'OrdersProducts';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['products_id', 'orders_id', 'count', 'totalPrice'], 'required'],
            [['products_id', 'orders_id', 'count', 'totalPrice'], 'integer'],
            [['orders_id'], 'exist', 'skipOnError' => true, 'targetClass' => Orders::class, 'targetAttribute' => ['orders_id' => 'id']],
            [['products_id'], 'exist', 'skipOnError' => true, 'targetClass' => Products::class, 'targetAttribute' => ['products_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'products_id' => 'Id Products',
            'orders_id' => 'Id Orders',
            'count' => 'Count',
            'totalPrice' => 'Total Price',
        ];
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasOne(Orders::class, ['id' => 'orders_id']);
    }

    /**
     * Gets query for [[Products]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasOne(Products::class, ['id' => 'products_id']);
    }

}
