<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Products".
 *
 * @property int $id
 * @property string $name
 * @property int $category_id
 * @property int $quantity
 * @property int $price
 *
 * @property Category $category
 * @property ImagesProducts[] $imagesProducts
 * @property OrdersProducts[] $ordersProducts
 * @property ProductBasket[] $productBaskets
 */
class Products extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Products';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['name', 'category_id', 'quantity', 'price'], 'required'],
            [['category_id', 'quantity', 'price'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['category_id'], 'exist', 'skipOnError' => true, 'targetClass' => Category::class, 'targetAttribute' => ['category_id' => 'id']],
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
            'category_id' => 'Category ID',
            'quantity' => 'Quantity',
            'price' => 'Price',
        ];
    }

    /**
     * Gets query for [[Category]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCategory()
    {
        return $this->hasOne(Category::class, ['id' => 'category_id']);
    }

    /**
     * Gets query for [[ImagesProducts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getImagesProducts()
    {
        return $this->hasMany(ImagesProducts::class, ['product_id' => 'id']);
    }

    /**
     * Gets query for [[OrdersProducts]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrdersProducts()
    {
        return $this->hasMany(OrdersProducts::class, ['id_products' => 'id']);
    }

    /**
     * Gets query for [[ProductBaskets]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getProductBaskets()
    {
        return $this->hasMany(ProductBasket::class, ['id_products' => 'id']);
    }

}
