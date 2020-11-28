<?php

/* @var $this \yii\web\View */
/* @var $content string */

use frontend\assets\AppAsset;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\bootstrap4\Breadcrumbs;
use common\widgets\Alert;
use common\models\User;
use yii\bootstrap4\Modal;

$asset = AppAsset::register($this);

$session = Yii::$app->session;
$class_menu = '';
$class_menu = $session['class_menu'];
$fav_url = $asset->baseUrl.'/images/favicons';

if (Yii::$app->controller->action->id === 'login') {

    echo $this->render(
        'main-login',
        ['content' => $content]
    );
} else {

    ?>
    <?php $this->beginPage() ?>
    <!DOCTYPE html>
    <html lang="<?= Yii::$app->language ?>">
    <head>
        <meta charset="<?= Yii::$app->charset ?>">
        <meta http-equiv="X-UA-Compatible" content="IE=edge">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <link rel="icon" type="image/png" sizes="32x32" href="<?= $fav_url ?>/favicon-32x32.png">
        <link rel="icon" type="image/png" sizes="16x16" href="<?= $fav_url ?>/favicon-16x16.png">
<!--        <link rel="manifest" href="--><?//= $fav_url ?><!--/site.webmanifest">-->

        <?php $this->registerCsrfMetaTags() ?>
        <title><?= Html::encode($this->title) ?></title>
        <?php $this->head() ?>
    </head>
    <style>
        .navbar > .container, .navbar > .container-fluid{
            max-width: 100%;
            margin: 0px;
        }
        .navbar-light .navbar-nav .nav-link{
            color: #222d32;
        }
        .nav-link a{
            text-decoration: none;
        }
        .navbar-light .navbar-nav .nav-link:hover, .nav-link a:hover{
            color: #007bff;
        }
        .navbar span {
            font-weight: 500;
        }
    </style>
    <body>
    <?php $this->beginBody() ?>
    <?php if (!Yii::$app->user->isGuest):?>
        <?php $profile = Yii::$app->user->identity->userProfile;?>
        <div class="wrapper">
            <nav id="sidebar" class="<?=$class_menu?>">
                <?= $this->render('left.php')?>
            </nav>

            <div id="content">

                <?php
                Modal::begin([
                    'title'=>'<h3 id="modalHeader"></h3>',
                    'id'=>'modal',
                    'size'=>'modal-lg',
                ]);
                echo "<div id='modalContent'></div>";
                Modal::end();


                $curentUser = Yii::$app->user->identity;

                NavBar::begin([
                    'id' => 'top-navbar',
                    'options' => [
                        'class' => 'navbar navbar-expand-lg navbar-light bg-light',
                    ],
                ]);

                echo '<span>Текущий пользователь: '.$profile->fullName.'</span>';
                $menuItems[] = '<li class="nav-link">'
                    .Html::a(
                        Html::tag('i', ' Профиль', ['value'=>Url::to('/site/change-password'), 'title' => 'Изменение пароля', 'class' => 'fas fa-user modalButton']),
                        '#'
                    )
                    .'</li>';

                $menuItems[] = '<li class="nav-link">'
                    .Html::a(
                        Html::tag('i', ' (выйти)', ['class' => 'fas fa-power-off', 'title' => 'Выход ('.$curentUser->userProfile->getFullName().')']),
                        ['/site/logout'],
                        ['data-method' => 'post']
                    )
                    .'</li>';

                echo Nav::widget([
                    'options' => ['class' =>'nav navbar-nav ml-auto', 'id' => 'top-nav-item-1'],
                    'items' => $menuItems,
                ]);
                NavBar::end();
                ?>

                <div>
                    <?= Alert::widget() ?>
                    <?= $content ?>
                </div>
            </div>
        </div>
    <?php else:?>
        <div class="flex-main-content">
            <?= $content ?>
        </div>
    <?php endif?>

    <?php $this->endBody() ?>
    </body>
    </html>
    <?php $this->endPage() ?>
<?php } ?>












