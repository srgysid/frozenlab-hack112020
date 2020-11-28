<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;

/**
 * This is the model class for table "order_performer".
 *
 * @property int $id
 * @property int|null $order_id
 * @property int|null $card_id
 *
 * @property Card $card
 * @property Orders $orders
 */
class OrdersPerformer extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders_performer';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['orders_id', 'card_id'], 'default', 'value' => null],
            [['orders_id', 'card_id'], 'integer'],
            [['card_id'], 'exist', 'skipOnError' => true, 'targetClass' => Card::className(), 'targetAttribute' => ['card_id' => 'id']],
            [['orders_id'], 'exist', 'skipOnError' => true, 'targetClass' => Orders::className(), 'targetAttribute' => ['orders_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'orders_id' => 'Order ID',
            'card_id' => 'Card ID',
        ];
    }

    /**
     * Gets query for [[Card]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCard()
    {
        return $this->hasOne(Card::className(), ['id' => 'card_id']);
    }

    /**
     * Gets query for [[Order]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasOne(Orders::className(), ['id' => 'orders_id']);
    }

    public static function getOrdersPerformerByOrdersId($orders_id) {
        $performer_ids = OrdersPerformer::find()->where(['orders_id' => $orders_id])->all();
        $performer_ids = ArrayHelper::getColumn($performer_ids, 'card_id');
        return $performer_ids;
    }

}
