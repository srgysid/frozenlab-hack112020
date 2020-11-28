<?php

use frontend\assets\AppAsset;
use yii\helpers\Html;
use yii\bootstrap4\Modal;

AppAsset::register($this);

/* @var $this yii\web\View */

$this->title = Yii::t('app','ИС Распоряжения');
//$user = Yii::$app->user;
//$profile = Yii::$app->user->identity->userProfile;
//echo '<pre>'.print_r($user, true).'</pre>';
//echo '<pre>'.print_r($profile->department_id, true).'</pre>';
?>
<div class="site-index">
    <?php
        if (Yii::$app->user->can('rl_admin')) {
//            echo 'привет, насяльника';
        }
    ?>
</div>
