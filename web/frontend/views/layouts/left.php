<?php

use frontend\assets\AppAsset;
use common\widgets\MenuRole;
use kartik\sidenav\SideNav;
use yii\helpers\Url;

$appAsset = AppAsset::register($this);

$img_path = $appAsset->baseUrl.'/images/msz_logo.png';
$img_path_s = $appAsset->baseUrl.'/images/new_logo_2.png';
//$session = Yii::$app->session;
//$class_menu = '';
//$class_menu = $session['class_menu'];
?>

<div class="sidebar-header">
    <div class="text-right">
        <button type="button" title="Сложить меню" id="sidebarCollapse" class="btn btn-default">
            <i class="fas fa-bars"></i>
        </button>
    </div>
    <div class="text-center">
        <h3>
            <a href="<?=Url::home()?>" title="Главная">
                <img align="center" width="200px" src="<?=$img_path?>">
            </a>
        </h3>
        <strong>
            <a href="<?=Url::home()?>" title="Главная">
                <img width="90px" src="<?=$img_path_s?>">
            </a>
        </strong>
    </div>
</div>


<?= MenuRole::widget(
    [
        'type' => MenuRole::TYPE_DEFAULT,
        'encodeLabels' => false,
        'iconPrefix' => 'fas fa-',
        'indItem' => false,
        'indMenuOpen' => '<i class="fas fa-caret-up"></i>',
        'indMenuClose' => '<i class="fas fa-caret-down"></i>',
        'items' => [
            [
                'label' => '<span class="CTAs">Распоряжения</span>',
                'url' =>  ['/orders/index'],
                'icon' => 'file-alt',
                'access' => ['rl_admin', 'rl_key_user', 'rl_view_user', 'rl_chief'],
            ],
//            [
//                'label' => '<span class="CTAs">Пункт 3</span>',
//                'url' => '#',
//                'icon' => 'tachometer-alt'
//            ],

            [
                'label' => '<span class="CTAs">Справочники</span>',
                'url' => '#',
                'icon' => 'book',
                'access' => ['rl_admin', 'rl_key_user'],
                'items' => [
                    [
                        'label' => '<span class="mini">Подразделения</span>',
                        'url' => ['/department/index']
                    ],
                    [
                        'label' => '<span class="mini">Виды поручений</span>',
                        'url' => ['/type-order/index']
                    ],
                    [
                        'label' => '<span class="mini">Сообщения по видам поручений</span>',
                        'url' => ['/type-message/index']
                    ],

                ]
            ],
            [
                'label' => '<span class="CTAs">Пользователи</span>',
                'url' => '#',
                'icon' => 'graduation-cap',
                'access' => ['rl_admin'],
                'items' => [
                    [
                        'label' => '<span class="mini">Пользователи</span>',
//                        'label' => 'Пользователи',
                        'url' => ['/employee/index']
                    ],
                ]
            ],
            [
                'label' => '<span class="CTAs">Служебные сервисы</span>',
                'url' => '#',
                'icon' => 'cogs',
                'access' => ['rl_admin'],
                'items' => [
                    [
                        'label' => '<span class="mini">Синхронизация с SAP</span>',
                        'url' => '#',
//                        'url' => ['/tables-import/index'],
                    ],
                ]
            ],
        ],

    ]
);
?>

