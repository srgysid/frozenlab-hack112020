<?php

namespace common\models;

use Yii;
use yii\db\Expression;
use yii\db\Query;

/**
 * This is the model class for table "type_message".
 *
 * @property int $id
 * @property int $type_order_id
 * @property string $name
 *
 * @property TypeOrder $typeOrder
 */
class TypeMessage extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'type_message';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type_order_id', 'name'], 'required'],
            [['type_order_id'], 'default', 'value' => null],
            [['type_order_id'], 'integer'],
            [['name'], 'string', 'max' => 255],
            [['type_order_id'], 'exist', 'skipOnError' => true, 'targetClass' => TypeOrder::className(), 'targetAttribute' => ['type_order_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_order_id' => 'Вид поручения',
            'name' => 'Сообщение',
        ];
    }

    /**
     * Gets query for [[TypeOrder]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTypeOrder()
    {
        return $this->hasOne(TypeOrder::className(), ['id' => 'type_order_id']);
    }

    public static function getTypeMessageByTypeOrder($type_order_id)
    {
        if (!$type_order_id) return [];

        $out = new Query();
        $out->addSelect([
            'type_message.id as id',
            'type_message.name as name',
        ])->from('type_message')
            ->where(['type_order_id' => $type_order_id])
            ->orderBy('name');
        return $out->all();
    }


}
