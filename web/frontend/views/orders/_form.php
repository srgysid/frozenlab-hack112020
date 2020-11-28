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


$index = 0;
$orderFileLink = [];
$orderFileLinkConfig = [];
foreach ($uploadFiles as $file){
    $orderFileLink[$index] = Url::to(['/orders/get-single-file','filePath' => $filePath.'/'.$file['id'].'.'.$file['file_ext'],'fileName' => $file['original_name']]);
    $orderFileLinkConfig[$index]['caption'] = $file['original_name'];
    $orderFileLinkConfig[$index]['key'] = $file['id'];

    switch ($file['file_ext']){
        case 'pdf':
            $orderFileLinkConfig[$index]['type'] = $file['file_ext'];
            $orderFileLinkConfig[$index]['size'] = $file['file_size'];
            break;
    }
    $index++;
}

//echo '<pre>'.print_r($model->typeMessage->name, true).'</pre>';
//echo '<pre>'.print_r($model->typeMessage->typeOrder->name, true).'</pre>';
//echo '<pre>'.print_r($cards, true).'</pre>';
//echo '<pre>'.print_r($cards2, true).'</pre>';

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
            <?= $form->field($model, 'type_cards')->dropDownList(Orders::getTypeCards())?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'priority')->dropDownList(Orders::getPriority())?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'type_performers')->dropDownList(Orders::getTypePerformers())?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'performer_ids')->widget(Select2::className(), [
                'data' => $cards,
                'maintainOrder' => true,
                'options' => [
                    'id' => 'performer-id',
                    'placeholder' => 'Выберите из списка',
                    'multiple' => true
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])
            ?>
        </div>

    </div>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'reaction')->dropDownList(Orders::getReaction())?>
        </div>

        <div class="col-md-3">
            <?= $form->field($model, 'typeOrder_id')->widget(Select2::className(), [
                'data' => $typeOrder,
                'options' => [
                    'id' => 'type-order-id',
                    'placeholder' => 'Выберите из списка'
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'type_message_id')->widget(DepDrop::classname(), [
                'type' => DepDrop::TYPE_SELECT2,
                'data' => $typeMessage,
                'options' => ['id' => 'type-message-id', 'placeholder' => '--',],
                'pluginOptions' => [
                    'depends' => ['type-order-id'],
                    'placeholder' => 'Выберите из списка',
                    'url' => Url::to(['/orders/type-order-list'])
                ]
            ]) ?>
        </div>

    </div>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'required_date')->widget(DateControl::classname(), [
                'options' => ['placeholder' => 'Введите дату ...'],
                'type' => DateControl::FORMAT_DATETIME,
                'displayFormat' => 'php:d.m.Y H:i',
                'saveFormat' => 'php:Y-m-d H:i:sO',
//            'displayTimezone' => Yii::$app->user->identity->userProfile->timezone,
                'widgetOptions' => [
                    'pluginOptions' => [
                        'autoclose' => true,
                        'startDate' => date('Y-m-d H:i')
                    ],
                    'options' => ['autocomplete' => 'off']
                ]
            ]) ?>

        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'fact_date')->widget(DateControl::classname(), [
                'options' => ['placeholder' => 'Введите дату ...'],
                'type' => DateControl::FORMAT_DATETIME,
                'displayFormat' => 'php:d.m.Y H:i',
                'saveFormat' => 'php:Y-m-d H:i:sO',
//            'displayTimezone' => Yii::$app->user->identity->userProfile->timezone,
                'widgetOptions' => [
                    'pluginOptions' => [
                        'autoclose' => true,
                        'startDate' => date('Y-m-d H:i')
                    ],
                    'options' => ['autocomplete' => 'off']
                ]
            ]) ?>
        </div>

        <div class="col-md-6">
            <div class="form-group">
                <label class="control-label" for="department_id"><?= $model->getAttributeLabel('department_id')?></label>
                <input type="text" id="department_id" class="form-control" readonly value="<?= ($model->department_id)? Html::encode($model->department->code.' '.$model->department->name): ""?>">
            </div>
        </div>

        <div class="col-md-12">
            <?= $form->field($model, 'short_desc')->textInput(['placeholder' => 'Заголовок']) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'full_desc')->textarea([
                'rows' => 4,
                'placeholder' => 'Комментарий',
            ]) ?>
        </div>

    </div>
    <?php
        $this->endBlock('mainData');
        $mainData = $this->blocks['mainData'];
    ?>

    <?php $this->beginBlock('filesData'); ?>
    <div class="row">
        <div class="col-md-12">
            <div id="file-input-id">
                <?php
                    echo FileInput::widget([
                        'model' => $model,
                        'attribute' => 'orderDocFiles[]',
                        'pluginOptions' => [
                            'deleteUrl' => Url::to(['/orders/delete-orders-doc-file']),
                            'initialPreviewDownloadUrl' => Url::to(['/orders/download-file']),
                            'initialPreview' => $orderFileLink,
                            'initialPreviewConfig' => $orderFileLinkConfig,
                            'initialPreviewAsData' => true,
                            'overwriteInitial' => false,
                            'showRemove' => true,
                            'showUpload' => false,
                            'allowedFileExtensions'=>['pdf','jpeg','jpg','png'],
                            'previewFileType' => ['pdf','jpeg','jpg','png'],
                        ],
                        'options' => [
                            'accept' => 'pdf|jpeg|jpg|png',
                            'multiple' => true,
                            'maxFileCount' => 16,
                            'maxFileSize'=>20000,
                        ],
                    ]);
                ?>
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
