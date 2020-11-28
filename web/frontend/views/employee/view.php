<?php

use yii\helpers\Html;
use common\helpers\AuthHelper;
use kartik\form\ActiveForm;
use yii\widgets\MaskedInput;

//$form->field($model, 'username', ['addon' => ['append' => ['content' => '@'.Yii::$app->params['username_domain']]],])->textInput()

//echo '<pre>'.print_r($model, true).'</pre>';
if ($model->card_id) $modelCard = \common\models\Card::findOne($model->card_id);
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
                        <div class="form-group">
                            <label class="control-label" for="card_id"><?= $model->getAttributeLabel('card_id')?></label>
                            <input type="text" id="card_id" class="form-control" readonly value="<?= ($model->card_id)? Html::encode($model->getFullName()): ""?>">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <?= $form->field($model, 'username')->textInput(['readonly' => true])?>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label" for="department_id"><?= $model->getAttributeLabel('department_id')?></label>
                            <input type="text" id="department_id" class="form-control" readonly value="<?= ($model->department_name)? Html::encode($model->department_name): ""?>">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label" for="staffpos_name"><?= $model->getAttributeLabel('staffpos_name')?></label>
                            <input type="text" id="staffpos_name" class="form-control" readonly value="<?= ($modelCard)? Html::encode($modelCard->getDataByCard($modelCard->stabnum)['staffpos_description']): ""?>">
                        </div>
                    </div>
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
                            'options' => [
                                'readonly' => true,
                            ],
                        ])
                        ?>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label" for="role"><?= $model->getAttributeLabel('role')?></label>
                            <input type="text" id="role" class="form-control" readonly value="<?= ($model->role)? AuthHelper::getRoles()[$model->role]: ""?>">
                        </div>
                    </div>
                    <div class="col-md-12">
                        <div class="form-group">
                            <label class="control-label" for="sign_chief"><?= $model->getAttributeLabel('sign_chief')?></label>
                            <input type="text" id="sign_chief" class="form-control" readonly value="<?= ($model->sign_chief)? 'Да': 'Нет' ?>">
                        </div>
                    </div>
                </div>
            </div>
    </div>

    <div class="modal-footer">
        <button type="button" class="btn btn-ppu btn-danger" data-dismiss="modal">Закрыть</button>
    </div>

    <?php ActiveForm::end(); ?>

</div>
