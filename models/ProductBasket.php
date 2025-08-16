<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ProductBasket".
 *
 * @property int $id
 * @property int $products_id
 * @property int $basket_id
 * @property int $count
 * @property int $totalPrice
 *
 * @property Baskets $basket
 * @property Products $products
 */
class ProductBasket extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'ProductBasket';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['products_id', 'basket_id', 'count', 'totalPrice'], 'required'],
            [['products_id', 'basket_id', 'count', 'totalPrice'], 'integer'],
            [['basket_id'], 'exist', 'skipOnError' => true, 'targetClass' => Baskets::class, 'targetAttribute' => ['basket_id' => 'id']],
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
            'basket_id' => 'Id Basket',
            'count' => 'Count',
            'totalPrice' => 'Total Price',
        ];
    }

    /**
     * Gets query for [[Basket]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getBasket()
    {
        return $this->hasOne(Baskets::class, ['id' => 'basket_id']);
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
