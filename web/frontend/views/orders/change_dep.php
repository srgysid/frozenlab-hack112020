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


?>

<div>
    <?php $form = ActiveForm::begin(
        [
            'id' => 'change-dep-form-id',
            'enableAjaxValidation' => true,
            'options' => ['enctype' => 'multipart/form-data'],
        ]

    ); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'department_id')->widget(Select2::className(), [
                'data' => $departments,
                'options' => [
                    'id' => 'department-id',
                    'placeholder' => 'Выберите из списка',
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ])
            ?>
        </div>
    </div>
    <div class="modal-footer">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-ppu btn-success']) ?>
        <button type="button" class="btn btn-ppu btn-danger" data-dismiss="modal">Закрыть</button>
    </div>

    <?php ActiveForm::end(); ?>

</div>
