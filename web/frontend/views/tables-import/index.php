<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap4\ActiveForm;
use frontend\assets\AppAsset;

$appAsset = AppAsset::register($this);

$this->title = Yii::t('app', 'Синхронизация таблиц');
$curentUser = Yii::$app->user->identity;


//echo '<pre>'.print_r($model, true).'</pre>';
?>
<div >
    <div class="row">
        <div  class="col-12">
            В таблицу "Карточки" обнавлено/импортированно: <?= $model->importSuccess['card']?> строк, ошибок: <?= $model->importError['card']?>
        </div>
        <div  class="col-12">
            В таблицу "Подразделения" обнавлено/импортированно: <?= $model->importSuccess['department']?> строк, ошибок: <?= $model->importError['department']?>
        </div>
        <div  class="col-12">
            В таблицу "Профессии" обнавлено/импортированно: <?= $model->importSuccess['staffpos']?> строк, ошибок: <?= $model->importError['staffpos']?>
        </div>
        <div  class="col-12">
            В таблицу "Перемещения" обнавлено/импортированно: <?= $model->importSuccess['movement']?> строк, ошибок: <?= $model->importError['movement']?>
        </div>

    </div>
</div>
