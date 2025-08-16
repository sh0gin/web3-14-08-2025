<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "Reason_cancellation".
 *
 * @property int $id
 * @property int $order_id
 * @property string $text
 *
 * @property Orders $order
 */
class ReasonCancellation extends \yii\db\ActiveRecord
{


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'Reason_cancellation';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_id', 'text'], 'required'],
            [['order_id'], 'integer'],
            [['text'], 'string'],
            [['order_id'], 'exist', 'skipOnError' => true, 'targetClass' => Orders::class, 'targetAttribute' => ['order_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'order_id' => 'Order ID',
            'text' => 'Text',
        ];
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrder()
    {
        return $this->hasOne(Orders::class, ['id' => 'order_id']);
    }
    

}
