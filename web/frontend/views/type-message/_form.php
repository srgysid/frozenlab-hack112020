<?php

use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use kartik\select2\Select2;
use kartik\depdrop\DepDrop;

//echo '<pre>'.print_r($model, true).'</pre>';

?>

<div >

    <?php $form = ActiveForm::begin(
        [
            'id' => 'type-message-form-id',
            'enableAjaxValidation' => true,
        ]
    ); ?>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'type_order_id')->widget(Select2::className(), [
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
        <div class="col-md-12">
            <?= $form->field($model, 'name')->textInput(['placeholder' => 'Текст сообщения']) ?>
        </div>
    </div>

    <div class="modal-footer">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-ppu btn-success']) ?>
        <button type="button" class="btn btn-ppu btn-danger" data-dismiss="modal">Закрыть</button>
    </div>

    <?php ActiveForm::end(); ?>

</div>
