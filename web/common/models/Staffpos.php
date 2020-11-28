<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "staffpos".
 *
 * @property int $id
 * @property string|null $staffpos_item_id
 * @property string|null $name
 * @property string|null $description
 * @property string|null $begin
 * @property string|null $end
 */
class Staffpos extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'staffpos';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['begin', 'end'], 'safe'],
            [['staffpos_item_id'], 'string', 'max' => 8],
            [['name'], 'string', 'max' => 40],
            [['description'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'staffpos_item_id' => 'Staffpos Item ID',
            'name' => 'Наименование',
            'description' => 'Описание',
            'begin' => 'Begin',
            'end' => 'End',
        ];
    }
}
