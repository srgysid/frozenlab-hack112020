<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "type_order".
 *
 * @property int $id
 * @property string $name
 *
 * @property TypeMessage[] $typeMessages
 */
class TypeOrder extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'type_order';
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
            'name' => 'Наименование',
        ];
    }

    /**
     * Gets query for [[TypeMessages]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTypeMessages()
    {
        return $this->hasMany(TypeMessage::className(), ['type_order_id' => 'id']);
    }
}
