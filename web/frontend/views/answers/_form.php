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
$answerFileLink = [];
$answerFileLinkConfig = [];
foreach ($uploadFiles as $file){
    $answerFileLink[$index] = Url::to(['/answers/get-single-file','filePath' => $filePath.'/'.$file['id'].'.'.$file['file_ext'],'fileName' => $file['original_name']]);
    $answerFileLinkConfig[$index]['caption'] = $file['original_name'];
    $answerFileLinkConfig[$index]['key'] = $file['id'];

    switch ($file['file_ext']){
        case 'pdf':
            $answerFileLinkConfig[$index]['type'] = $file['file_ext'];
            $answerFileLinkConfig[$index]['size'] = $file['file_size'];
            break;
    }
    $index++;
}

?>

<div>
    <?php $form = ActiveForm::begin(
        [
            'id' => 'answers-form-id',
            'enableAjaxValidation' => true,
            'options' => ['enctype' => 'multipart/form-data'],
        ]

    ); ?>

    <?php $this->beginBlock('mainData'); ?>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'orders_id')->widget(Select2::className(), [
                'data' => $orders,
                'maintainOrder' => true,
                'options' => [
                    'id' => 'orders-id',
                    'placeholder' => 'Выберите из списка',
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])
            ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'status_id')->dropDownList(\common\models\Answers::getStatus())?>
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
                        'attribute' => 'answerDocFiles[]',
                        'pluginOptions' => [
                            'deleteUrl' => Url::to(['/answers/delete-answers-doc-file']),
                            'initialPreviewDownloadUrl' => Url::to(['/answers/download-file']),
                            'initialPreview' => $answerFileLink,
                            'initialPreviewConfig' => $answerFileLinkConfig,
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
                'label' => 'Информация ответа',
                'content' => $mainData,
                'active' => true
            ],
            [
                'label' => 'Файлы ответа',
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
