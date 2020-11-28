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
 * This is the model class for table "order".
 *
 * @property int $id
 * @property int|null $type_cards
 * @property int|null $type_performers
 * @property int|null $priority
 * @property string|null $short_desc
 * @property string $required_date
 * @property string $fact_date
 * @property int|null $reaction
 * @property int|null $type_message_id
 * @property string|null $full_desc
 * @property int|null $department_id
 * @property int|null $closer_id
 * @property int $creator_id
 * @property int $updater_id
 * @property string $created_at
 * @property string $updated_at
 * @property string $closed_at
 * @property int $status_id
 *
 * @property Department $department
 * @property TypeMessage $typeMessage
 * @property User $closer
 * @property User $creator
 * @property User $updater
 * @property OrderPerformer[] $orderPerformers
 */
class Orders extends \yii\db\ActiveRecord
{
    public $performer_ids;
    public $orderDocFiles;
    public $typeOrder_id;

    const CARD_CONSTANT_PERIODIC = 1;
    const CARD_MESSAGE = 2;
    const CARD_INSTRUCTIONS = 3;

    const PERFORMERS_ALL = 1;
    const PERFORMERS_CURRENTS = 2;

    const PRIORITY_MOST_IMPORTANT = 1;
    const PRIORITY_HIGH = 2;
    const PRIORITY_MEDIUM = 3;
    const PRIORITY_LOW = 4;

    const REACTION_ANSWER = 1;
    const REACTION_INFORMATION = 2;
    const REACTION_ACCEPTED = 3;

    const STATUS_CREATED = 1;
    const STATUS_IN_PROGRESS = 2;
    const STATUS_FINISHED = 3;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'orders';
    }

    /**
     * {@inheritdoc}
     */

    public function init()
    {
        parent::init();
        $this->on(self::EVENT_AFTER_INSERT, [$this, 'sendMail']);
    }

    public function rules()
    {
        return [
            [['type_cards', 'type_performers', 'priority', 'reaction', 'type_message_id', 'department_id', 'closer_id', 'creator_id', 'updater_id', 'status_id'], 'default', 'value' => null],
            [['type_cards', 'type_performers', 'priority', 'reaction', 'type_message_id', 'department_id', 'closer_id', 'creator_id', 'updater_id', 'status_id'], 'integer'],
            [['type_cards', 'type_performers', 'priority'], 'required'],
            [['required_date', 'fact_date', 'created_at', 'updated_at', 'closed_at'], 'safe'],
            [['full_desc'], 'string'],
            [['short_desc'], 'string', 'max' => 255],
            [['department_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::className(), 'targetAttribute' => ['department_id' => 'id']],
            [['type_message_id'], 'exist', 'skipOnError' => true, 'targetClass' => TypeMessage::className(), 'targetAttribute' => ['type_message_id' => 'id']],
            [['closer_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['closer_id' => 'id']],
            [['creator_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['creator_id' => 'id']],
            [['updater_id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['updater_id' => 'id']],

            [['performer_ids'], 'each', 'rule' => ['exist', 'skipOnError' => true, 'targetClass' => Card::className(), 'targetAttribute' => ['performer_ids' => 'id']]],
            [['orderDocFiles'], 'file', 'skipOnEmpty' => true, 'extensions' => 'pdf, jpeg, jpg, png', 'maxFiles' => 16],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'type_cards' => 'Тип карточки',
            'type_performers' => 'Тип исполнителя',
            'priority' => 'Приоритет',
            'short_desc' => 'Заголовок',
            'required_date' => 'Необходимый срок исполнения',
            'fact_date' => 'Фактический срок',
            'reaction' => 'Реакция',
            'type_message_id' => 'Сообшение',
            'full_desc' => 'Комментарий',
            'department_id' => 'Подразделение',
            'closer_id' => 'Closer ID',
            'creator_id' => 'Creator ID',
            'updater_id' => 'Updater ID',
            'created_at' => 'Дата/время регистрации',
            'updated_at' => 'Updated At',
            'closed_at' => 'Closed At',
            'performer_ids' => 'Исполнители',
            'typeOrder_id' => 'Вид поручения',
            'status_id' => 'Статус распоряжения',
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

    public static function getTypeCards()
    {
        return [
            '1' => Yii::t('app', 'Постоянные/периодические заявки и поручения'),
            '2' => Yii::t('app', 'Информационное сообщение'),
            '3' => Yii::t('app', 'Поручение'),
        ];
    }

    public static function getTypePerformers()
    {
        return [
            '1' => Yii::t('app', 'Все исполнители'),
            '2' => Yii::t('app', 'Конкретный исполнитель(и)'),
        ];
    }

    public static function getPriority()
    {
        return [
            '1' => Yii::t('app', 'Особо важный'),
            '2' => Yii::t('app', 'Высокий'),
            '3' => Yii::t('app', 'Средний'),
            '4' => Yii::t('app', 'Низкий'),
        ];
    }

    public static function getReaction()
    {
        return [
            '1' => Yii::t('app', 'ответ'),
            '2' => Yii::t('app', 'к сведению'),
            '3' => Yii::t('app', 'отметка «принято»'),
        ];
    }

    public static function getStatus()
    {
        return [
            '1' => Yii::t('app', 'новая'),
            '2' => Yii::t('app', 'в работе'),
            '3' => Yii::t('app', 'закрыта'),
        ];
    }

    public function getUploadPath()
    {
        return rtrim(Yii::getAlias('@frontend/uploads/orders/'.$this->id));
    }

    /**
     * Gets query for [[Department]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getDepartment()
    {
        return $this->hasOne(Department::className(), ['id' => 'department_id']);
    }

    /**
     * Gets query for [[TypeMessage]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getTypeMessage()
    {
        return $this->hasOne(TypeMessage::className(), ['id' => 'type_message_id']);
    }

    /**
     * Gets query for [[Closer]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getCloser()
    {
        return $this->hasOne(User::className(), ['id' => 'closer_id']);
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

    /**
     * Gets query for [[OrderPerformers]].
     *
     * @return \yii\db\ActiveQuery
     */
    public function getOrdersPerformers()
    {
        return $this->hasMany(OrdersPerformer::className(), ['orders_id' => 'id']);
    }

    public function saveOrders()
    {
        if ($this->save()){
            OrdersPerformer::deleteAll(['orders_id' => $this->id]);
            if ($this->type_performers == Orders::PERFORMERS_ALL) $this->performer_ids = [];
            if ($this->performer_ids) {
                foreach ($this->performer_ids as $performer_id) {
                    $modelOrdersPerformer = new OrdersPerformer();
                    $modelOrdersPerformer->orders_id = $this->id;
                    $modelOrdersPerformer->card_id = $performer_id;
                    $modelOrdersPerformer->save(false);
                }
            }
            return true;
        }
        return false;
    }

    public function saveOrdersDocFiles($fileMass, $update = null)
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

                $modelOrdersFiles->type_source = OrdersFiles::SOURCE_ORDERS;
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
                        $files['initialPreview'] = Url::to(['/orders/get-single-file','filePath' => $fileDir.'/'.$modelOrdersFiles->id.'.'.$modelOrdersFiles->file_ext,'fileName' => $modelOrdersFiles->original_name]);
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

    public function sendMail()
    {
        if ($this->type_performers == Orders::PERFORMERS_ALL) {
            $arrPerformers = Card::getDataByDepartment($this->department_id);
            if ($arrPerformers){
                foreach ($arrPerformers as $performer){
                    $profiles = UserProfile::find()->where(['card_id' => $performer->id])->all();
                    if ($profiles) {
                        foreach ($profiles as $profile){
                            $user = User::findOne($profile->user_id);
                            if ($user) {
                                MailHelper::notificationUser($this, $user, 'членом рабочей группы');
                            }
                        }
                    }
                }
            }
        }
        else {
            if ($this->performer_ids){
                foreach ($this->performer_ids as $performer_id){
                    $profiles = UserProfile::find()->where(['card_id' => $performer_id])->all();
                    if ($profiles) {
                        foreach ($profiles as $profile){
                            $user = User::findOne($profile->user_id);
                            if ($user) {
                                MailHelper::notificationUser($this, $user, 'членом рабочей группы');
                            }
                        }
                    }
                }
            }
        }
    }

}
