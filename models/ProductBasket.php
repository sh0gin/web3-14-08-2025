<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "ProductBasket".
 *
 * @property int $id
 * @property int $id_products
 * @property int $id_basket
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
            [['id_products', 'id_basket', 'count', 'totalPrice'], 'required'],
            [['id_products', 'id_basket', 'count', 'totalPrice'], 'integer'],
            [['id_basket'], 'exist', 'skipOnError' => true, 'targetClass' => Baskets::class, 'targetAttribute' => ['id_basket' => 'id']],
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
            'id_basket' => 'Id Basket',
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
        return $this->hasOne(Baskets::class, ['id' => 'id_basket']);
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
