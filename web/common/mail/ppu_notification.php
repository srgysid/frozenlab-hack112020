<?php
use yii\helpers\Html;
use yii\helpers\Url;

$link = Yii::$app->urlManager->createAbsoluteUrl(['/orders/view?id='.$model->id]);
$descLink = 'Распоряжения / Просмотр карты';

?>
<p><?= $model->typeMessage->name?>.</p>
<p>Ссылка на распоряжение <?= Html::a(Html::encode($descLink), $link) ?></p>
<p>С уважением, Служба поддержки пользователей</p>

