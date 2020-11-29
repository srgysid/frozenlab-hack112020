<?php

namespace common\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\helpers\ArrayHelper;
use common\helpers\MailHelper;
use yii\db\Expression;
use yii\db\Query;

use yii\helpers\BaseFileHelper;
use yii\helpers\Url;
use yii\web\HttpException;
use yii\web\UploadedFile;

/**
 * This is the model class for table "answers".
 *
 * @property int $id
 * @property int|null $orders_id
 * @property int|null $status_id
 * @property string|null $full_desc
 * @property int $creator_id
 * @property int $updater_id
 * @property string $created_at
 * @property string $updated_at
 *
 * @property Orders $orders
 * @property User $creator
 * @property User $updater
 */
class Answers extends \yii\db\ActiveRecord
{
    public $answerDocFiles;

    const STATUS_VIEW = 1;
    const STATUS_ACCEPTED = 2;
    const STATUS_IN_PROGRESS = 3;
    const STATUS_FINISHED = 4;
    const STATUS_FAIL = 5;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'answers';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['orders_id', 'status_id', 'creator_id', 'updater_id'], 'default', 'value' => null],
            [['orders_id', 'status_id', 'creator_id', 'updater_id'], 'integer'],
            [['full_desc'], 'string'],
            [['orders_id', 'status_id'], 'required'],
            [['created_at', 'updated_at'], 'safe'],
            [['orders_id'], 'exist', 'skipOnError' => true, 'targetClass' => Orders::className(), 'targetAttribute' => ['orders_id' => 'id']],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['creator_id' => 'id']],
            [['updater_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updater_id' => 'id']],
            [['answerDocFiles'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf, jpeg, jpg, png', 'maxFiles' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'orders_id' => 'Распоряжение',
            'status_id' => 'Статус',
            'full_desc' => 'Комментарий',
            'creator_id' => 'Исполнитель',
            'updater_id' => 'Updater ID',
            'created_at' => 'Дата создания',
            'updated_at' => 'Updated At',
        ];
    }

    public function behaviors()
    {
        $behaviors = parent::behaviors();
        $behaviors = ArrayHelper::merge($behaviors, [
            [
                'class' => TimestampBehavior::className(),
                'value' => new Expression('NOW()'),
            ],
        ]);
        return $behaviors;
    }

    public static function getStatus()
    {
        return [
            '1' => Yii::t('app', 'просмотренно'),
            '2' => Yii::t('app', 'принято'),
            '3' => Yii::t('app', 'в работе'),
            '4' => Yii::t('app', 'завершено'),
            '5' => Yii::t('app', 'провалено'),
        ];
    }


    public function getUploadPath()
    {
        return rtrim(Yii::getAlias('@frontend/uploads/answers/'.$this->id));
    }

    /**
     * Gets query for [[Orders]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasOne(Orders::className(), ['id' => 'orders_id']);
    }

    /**
     * Gets query for [[Creator]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCreator()
    {
        return $this->hasOne(User::className(), ['id' => 'creator_id']);
    }

    /**
     * Gets query for [[Updater]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getUpdater()
    {
        return $this->hasOne(User::className(), ['id' => 'updater_id']);
    }

    public function saveAnswersDocFiles($fileMass, $update = null)
    {
        $id = $this->id;
        $fileDir = $this->getUploadPath();
        if (!BaseFileHelper::createDirectory($fileDir)) {
            Yii::$app->getSession()->addFlash('error', 'Could not create directory ' . $fileDir);
            return false;
        }

        $filePath = '';
        $files =[];
        foreach ($fileMass as $file) {
            $transaction = Yii::$app->db->beginTransaction();
            try {
                $modelOrdersFiles = new OrdersFiles();

                $modelOrdersFiles->type_source = OrdersFiles::SOURCE_ANSWER;
                $modelOrdersFiles->source_id = $id;
                $modelOrdersFiles->file_type = $file->type;
                $modelOrdersFiles->file_ext = $file->extension;
                $modelOrdersFiles->original_name = $file->name;
                $modelOrdersFiles->file_size = $file->size;
                $modelOrdersFiles->save();

                if ($modelOrdersFiles) {
                    $filePath = $fileDir .'/'.$modelOrdersFiles['id'] . '.' . $file->extension;

                    if (!$file->saveAs($filePath)) {
                        $transaction->rollback();
                        Yii::$app->getSession()->addFlash('error', 'Could not save file ' . $filePath);
                        Yii::trace('FileSave.Error', $file->error);
                        return false;
                    }
                    $transaction->commit();

                    if ($update) {
                        $files['initialPreview'] = Url::to(['/answers/get-single-file','filePath' => $fileDir.'/'.$modelOrdersFiles->id.'.'.$modelOrdersFiles->file_ext,'fileName' => $modelOrdersFiles->original_name]);
                        $files['initialPreviewAsData'] = true;
                        $files['initialPreviewConfig'][]['key'] = $modelOrdersFiles->id;
                        $files['initialPreviewConfig'][]['caption'] = $modelOrdersFiles->original_name;
                        $files['initialPreviewConfig'][]['type'] = $modelOrdersFiles->file_ext;
                        $files['initialPreviewConfig'][]['size'] = $modelOrdersFiles->file_size;
                        return $files;
                        //                    return json_encode($files);
                    }
                } else {
                    throw new Exception('DB File Save error');
                }
            } catch (\Exception $e) {
                $transaction->rollback();
                Yii::$app->getSession()->addFlash('error', 'Could not save file ' . $filePath . '. ' . $e->getMessage());
                return false;
            }
        }
    }

}
