<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "movement".
 *
 * @property int $id
 * @property string|null $stabnum
 * @property string|null $staffpos_item_id
 * @property string|null $department_item_id
 * @property string|null $begin
 * @property string|null $end
 */
class Movement extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'movement';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['begin', 'end'], 'safe'],
            [['stabnum', 'staffpos_item_id', 'department_item_id'], 'string', 'max' => 8],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'stabnum' => 'Stabnum',
            'staffpos_item_id' => 'Staffpos Item ID',
            'department_item_id' => 'Department Item ID',
            'begin' => 'Begin',
            'end' => 'End',
        ];
    }
}
