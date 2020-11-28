<?php

use yii\bootstrap4\ActiveForm;
use yii\helpers\Html;

//echo '<pre>'.print_r($model, true).'</pre>';
?>

<div >

    <?php $form = ActiveForm::begin(
        [
            'id' => 'department-form-id',
            'enableAjaxValidation' => true,
        ]

    ); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'name')->textInput(['readonly' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'short_name')->textInput(['placeholder' => 'Краткое наименование']) ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'full_name')->textInput(['placeholder' => 'Полное наименование']) ?>
        </div>

    </div>

    <div class="modal-footer">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-ppu btn-success']) ?>
        <button type="button" class="btn btn-ppu btn-danger" data-dismiss="modal">Закрыть</button>
    </div>

    <?php ActiveForm::end(); ?>

</div>
