<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "OrdersProducts".
 *
 * @property int $id
 * @property int $id_products
 * @property int $id_orders
 * @property int $count
 * @property int $totalCount
 *
 * @property Orders $orders
 * @property Products $products
 */
class OrdersProducts extends \yii\db\ActiveRecord
{


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
            [['id_products', 'id_orders', 'count', 'totalCount'], 'required'],
            [['id_products', 'id_orders', 'count', 'totalCount'], 'integer'],
            [['id_orders'], 'exist', 'skipOnError' => true, 'targetClass' => Orders::class, 'targetAttribute' => ['id_orders' => 'id']],
            [['id_products'], 'exist', 'skipOnError' => true, 'targetClass' => Products::class, 'targetAttribute' => ['id_products' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_products' => 'Id Products',
            'id_orders' => 'Id Orders',
            'count' => 'Count',
            'totalCount' => 'Total Count',
        ];
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasOne(Orders::class, ['id' => 'id_orders']);
    }

    /**
     * Gets query for [[Products]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProducts()
    {
        return $this->hasOne(Products::class, ['id' => 'id_products']);
    }

}
