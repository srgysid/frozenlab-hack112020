<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;
use yii\helpers\Url;
use kartik\select2\Select2;
use kartik\depdrop\DepDrop;
use kartik\datecontrol\DateControl;
use kartik\file\FileInput;
use yii\bootstrap4\Tabs;

use common\models\Orders;

//echo '<pre>'.print_r($model->typeMessage->name, true).'</pre>';
//echo '<pre>'.print_r($model->typeMessage->typeOrder->name, true).'</pre>';
//echo '<pre>'.print_r($cards, true).'</pre>';
//echo '<pre>'.print_r($cards2, true).'</pre>';

$performersList = '';
$delimetr = '';
foreach ($modelOrdersPerformers as $performers) {
    $performersList = $performersList.$delimetr.Html::encode($performers->card->getFullName());
    $delimetr = ', ';
}

?>

<div>
    <?php $form = ActiveForm::begin(
        [
            'id' => 'orders-form-id',
            'enableAjaxValidation' => true,
            'options' => ['enctype' => 'multipart/form-data'],
        ]

    ); ?>

    <?php $this->beginBlock('mainData'); ?>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label" for="type_cards"><?= $model->getAttributeLabel('type_cards')?></label>
                <input type="text" id="type_cards" class="form-control" readonly value="<?= ($model->type_cards) ? Html::encode(Orders::getTypeCards()[$model->type_cards]): ""?>">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="priority"><?= $model->getAttributeLabel('priority')?></label>
                <input type="text" id="priority" class="form-control" readonly value="<?= ($model->priority) ? Html::encode(Orders::getPriority()[$model->priority]): ""?>">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="type_performers"><?= $model->getAttributeLabel('type_performers')?></label>
                <input type="text" id="type_performers" class="form-control" readonly value="<?= ($model->type_performers) ? Html::encode(Orders::getTypePerformers()[$model->type_performers]): ""?>">
            </div>
        </div>
        <?php if ($model->type_performers == Orders::PERFORMERS_CURRENTS):?>
        <div class="col-md-12">
            <div class="form-group">
                <label class="control-label" for="performer_ids"><?= $model->getAttributeLabel('performer_ids')?></label>
                <input type="text" id="performer_ids" class="form-control" readonly
                       title="<?= ($performersList) ? Html::encode($performersList): ""?>"
                       value="<?= ($performersList) ? Html::encode($performersList): ""?>"
                >
            </div>
        </div>
        <?php endif?>
    </div>
    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="reaction"><?= $model->getAttributeLabel('reaction')?></label>
                <input type="text" id="reaction" class="form-control" readonly value="<?= ($model->reaction) ? Html::encode(Orders::getReaction()[$model->reaction]): ""?>">
            </div>
        </div>

        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="typeOrder_id"><?= $model->getAttributeLabel('typeOrder_id')?></label>
                <input type="text" id="typeOrder_id" class="form-control" readonly value="<?= ($model->type_message_id) ? Html::encode($model->typeMessage->typeOrder->name): ""?>">
            </div>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label" for="type_message_id"><?= $model->getAttributeLabel('type_message_id')?></label>
                <input type="text" id="type_message_id" class="form-control" readonly value="<?= ($model->type_message_id) ? Html::encode($model->typeMessage->name): ""?>">
            </div>
        </div>

    </div>

    <div class="row">
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="required_date"><?= $model->getAttributeLabel('required_date')?></label>
                <input type="text" id="required_date" class="form-control" readonly value="<?= Yii::$app->formatter->asDate($model->required_date).' '.Yii::$app->formatter->asTime($model->required_date, 'php:H:i')?>">
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <label class="control-label" for="fact_date"><?= $model->getAttributeLabel('fact_date')?></label>
                <input type="text" id="fact_date" class="form-control" readonly value="<?= Yii::$app->formatter->asDate($model->fact_date).' '.Yii::$app->formatter->asTime($model->fact_date, 'php:H:i')?>">
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label" for="department_id"><?= $model->getAttributeLabel('department_id')?></label>
                <input type="text" id="department_id" class="form-control" readonly value="<?= ($model->department_id)? Html::encode($model->department->code.' '.$model->department->name): ""?>">
            </div>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'short_desc')->textInput(['readonly' => true]) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'full_desc')->textarea([
                'rows' => 4,
                'readonly' => true
            ]) ?>
        </div>

    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label" for="creator_id">Создано</label>
                <div class="row">
                    <div class="col-md-8">
                        <input type="text" id="creator_id" class="form-control"
                               title="<?= ($model->creator_id) ? Html::encode($model->creator->userProfile->fullName) : "" ?>"
                               value="<?= ($model->creator_id) ? Html::encode($model->creator->userProfile->fullName) : "" ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <input type="text" id="created_at" class="form-control" readonly value="<?= Yii::$app->formatter->asDate($model->created_at).' '.Yii::$app->formatter->asTime($model->created_at, 'php:H:i')?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label" for="updater_id">Изменено</label>
                <div class="row">
                    <div class="col-md-8">
                        <input type="text" id="updater_id" class="form-control"
                               title="<?= ($model->updater_id) ? Html::encode($model->updater->userProfile->fullName) : "" ?>"
                               value="<?= ($model->updater_id) ? Html::encode($model->updater->userProfile->fullName) : "" ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <input type="text" id="updated_at" class="form-control" readonly value="<?= Yii::$app->formatter->asDatetime($model->updated_at)?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
        $this->endBlock('mainData');
        $mainData = $this->blocks['mainData'];
    ?>

    <?php $this->beginBlock('filesData'); ?>
    <div class="row">
        <div class="col-md-12">
            <?php if ($uploadFiles): ?>
                <div class="col-md-12">
                    <div class="box-body">
                        <ul class="file-list">
                            <?php foreach($uploadFiles as $file): ?>
                                <li>
                                    <a href="<?= Url::to(['/orders/get-single-file','filePath' => $filePath.'/'.$file['id'].'.'.$file['file_ext'],'fileName' => $file['original_name']]) ?>" target="_blank"><?= Html::encode($file->original_name) ?></a>
                                    <small class="text-muted">(<?= Yii::$app->formatter->asDateTime($file->created_at) ?>)</small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            <?php endif; ?>

        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label" for="creator_id">Создано</label>
                <div class="row">
                    <div class="col-md-8">
                        <input type="text" id="creator_id" class="form-control"
                               title="<?= ($model->creator_id) ? Html::encode($model->creator->userProfile->fullName) : "" ?>"
                               value="<?= ($model->creator_id) ? Html::encode($model->creator->userProfile->fullName) : "" ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <input type="text" id="created_at" class="form-control" readonly value="<?= Yii::$app->formatter->asDate($model->created_at).' '.Yii::$app->formatter->asTime($model->created_at, 'php:H:i')?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label" for="updater_id">Изменено</label>
                <div class="row">
                    <div class="col-md-8">
                        <input type="text" id="updater_id" class="form-control"
                               title="<?= ($model->updater_id) ? Html::encode($model->updater->userProfile->fullName) : "" ?>"
                               value="<?= ($model->updater_id) ? Html::encode($model->updater->userProfile->fullName) : "" ?>" readonly>
                    </div>
                    <div class="col-md-4">
                        <input type="text" id="updated_at" class="form-control" readonly value="<?= Yii::$app->formatter->asDatetime($model->updated_at)?>">
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php
        $this->endBlock('filesData');
        $filesData = $this->blocks['filesData'];
    ?>

    <?php
    echo Tabs::widget([
        'items' => [
            [
                'label' => 'Информация по карточке',
                'content' => $mainData,
                'active' => true
            ],
            [
                'label' => 'Файлы по карточке',
                'content' => $filesData,
            ],
        ],
    ]);
    ?>

    <div class="modal-footer">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-ppu btn-success']) ?>
        <button type="button" class="btn btn-ppu btn-danger" data-dismiss="modal">Закрыть</button>
    </div>

    <?php ActiveForm::end(); ?>

</div>
