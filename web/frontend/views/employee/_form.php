<?php

use yii\helpers\Html;
use yii\helpers\Url;
//use yii\bootstrap4\ActiveForm;
use kartik\form\ActiveForm;
use kartik\select2\Select2;
use yii\widgets\MaskedInput;
use yii\helpers\ArrayHelper;
use common\helpers\AuthHelper;
use kartik\depdrop\DepDrop;
use kartik\file\FileInput;

$Js = <<<SCRIPT
$(document).ready(function () {
    $('#card-id').change(function() {
        var card_id = $('#card-id').val();
        if (card_id){
            $.ajax({
                url: '/employee/card-data',
                method: 'POST',
                data: {'card_id': card_id},
                success: function(data){	        
                    $('#employee-username').val(data['stubnum']);
                    $('#employee-department_name').val(data['department_name']);
                    $('#employee-staffpos_name').val(data['staffpos_name']);
                }
            });
        }
    });
});
SCRIPT;
$this->registerJs($Js);

//$form->field($model, 'username', ['addon' => ['append' => ['content' => '@'.Yii::$app->params['username_domain']]],])->textInput()

//echo '<pre>'.print_r($cards, true).'</pre>';
?>

<div class="employee-form">

    <?php $form = ActiveForm::begin(
        [
            'id' => 'employee-form-id',
            'enableAjaxValidation' => true,
        ]
    ); ?>

    <div class="row">
            <div class="col-md-6">
                <div class="row">

                    <div class="col-md-12">
                        <?= $form->field($model, 'card_id')->widget(Select2::className(), [
                            'data' => $cards,
                            'options' => [
                                'id' => 'card-id',
                                'placeholder' => 'Выберете из списка'
                            ],
                            'pluginOptions' => [
                                'allowClear' => true
                            ],
                        ]) ?>
                    </div>
                    <div class="col-md-12">
                        <?= $form->field($model, 'username')->textInput(['readonly' => true])?>
                    </div>
                    <div class="col-md-12">
                        <?= $form->field($model, 'department_name')->textInput(['readonly' => true])?>
                    </div>
                    <div class="col-md-12">
                        <?= $form->field($model, 'staffpos_name')->textInput(['readonly' => true])?>
                    </div>

                    <?php if ($model->scenario == \common\models\Employee::SCENARIO_REGISTER): ?>
                        <div class="col-md-12">
                            <?= $form->field($model, 'password')->passwordInput(['placeholder' => 'Введите пароль пользователя'])?>
                        </div>
                        <div class="col-md-12">
                            <?= $form->field($model, 'password_repeat')->passwordInput(['placeholder' => 'Повторно введите пароль пользователя'])?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <div class="col-md-6">
                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($model, 'phone')->widget(MaskedInput::className(),[
                            'mask'=>'+7 (999) 999-99-99',
                            'clientOptions' => [
                                'removeMaskOnSubmit' => true,
                                'autoUnmask' => true,
                            ],
                        ])
                        ?>
                    </div>
                    <div class="col-md-12">
                        <?= $form->field($model, 'role')
                            ->radioList($roles, [
                                'item' => function ($index, $label, $name, $checked, $value) {
                                    return '<label class="' . ($checked ? ' active' : '') . '">' .
                                        Html::radio($name, $checked, ['value' => $value, 'class' => 'project-status-btn']) . $label . '</label><br>';
                                },
                            ])
                        ?>
                    </div>
                    <div class="col-md-12">
                        <?= $form->field($model, 'sign_chief')->checkbox() ?>
                    </div>

                </div>
            </div>
    </div>

    <div class="modal-footer">
        <?= Html::submitButton(Yii::t('app', 'Сохранить'), ['class' => 'btn btn-ppu btn-success']) ?>
        <button type="button" class="btn btn-ppu btn-danger" data-dismiss="modal">Закрыть</button>
    </div>

    <?php ActiveForm::end(); ?>

</div>
