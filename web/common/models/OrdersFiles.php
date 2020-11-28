<?php

namespace common\models;

use Yii;
use yii\helpers\ArrayHelper;
use yii\behaviors\TimestampBehavior;
use yii\db\Expression;

/**
 * This is the model class for table "order_files".
 *
 * @property int $id
 * @property int $type_source
 * @property int $source_id
 * @property string $created_at
 * @property string|null $original_name
 * @property string|null $file_type
 * @property string|null $file_ext
 * @property int|null $file_size
 */
class OrdersFiles extends \yii\db\ActiveRecord
{
    const SOURCE_ORDERS = 1;
    const SOURCE_ANSWER = 2;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders_files';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['type_source', 'source_id'], 'required'],
            [['type_source', 'source_id', 'file_size'], 'default', 'value' => null],
            [['type_source', 'source_id', 'file_size'], 'integer'],
            [['created_at'], 'safe'],
            [['original_name', 'file_type', 'file_ext'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_source' => 'Type Source',
            'source_id' => 'Source ID',
            'created_at' => 'Created At',
            'original_name' => 'Original Name',
            'file_type' => 'File Type',
            'file_ext' => 'File Ext',
            'file_size' => 'File Size',
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors = ArrayHelper::merge($behaviors, [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
                'updatedAtAttribute' => false,
            ],
        ]);
        return $behaviors;
    }

    public function getSourceId()
    {
        return $this->source_id;
    }

    public function getFullName()
    {
        return $this->original_name . '.' . $this->file_ext;
    }

    public function getUploadDir($id, $type_source)
    {
        if ($type_source == OrdersFiles::SOURCE_ORDERS) return Yii::getAlias('@frontend/uploads/orders/'.$id);
        if ($type_source == OrdersFiles::SOURCE_ANSWER) return Yii::getAlias('@frontend/uploads/answers/'.$id);
    }

    public function getPath($fileDir)
    {
        return $fileDir.'/'.$this->id.'.'.$this->file_ext;
    }

    public static function deleteUploadFile($filePath)
    {
        // delete file from file system
        if (file_exists($filePath)) {
            unlink($filePath);
            return true;
        }
        return false;
    }

}
