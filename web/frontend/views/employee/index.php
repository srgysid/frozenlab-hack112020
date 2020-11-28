<?php

use kartik\grid\GridView;
use kartik\select2\Select2;
use backend\assets\AppAsset;
use yii\bootstrap4\ButtonDropdown;
use yii\bootstrap4\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use common\models\User;
use common\helpers\AuthHelper;

AppAsset::register($this);

$this->title = Yii::t('app', 'Сотрудники');
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user;

if ($user->can('rl_admin') or $user->can('rl_hl_user')) {
    $actionColumn = [
        'class' => 'kartik\grid\ActionColumn',
        'hiddenFromExport' => true,
        'contentOptions' => [
            'style' => 'width:80px;  min-width:80px; max-width:80px;'
        ],

        'buttons' => [
            'all' => function ($url, $model) {
                return '<div class="btn-group">'.ButtonDropdown::widget([
                    'label' => '...',
                    'options' => ['class' => 'btn btn-default dropleft'],
                    'dropdown' => [
                        'options' => ['class' => 'dropdown-menu'],
                        'items' => [
                            [
                                'label' => 'Редактировать профиль',
                                'url' => '#',
                                'linkOptions' => ['value'=> Url::to(['/employee/update-profile', 'id' => $model['user_id']]), 'title' => 'Редактирование профиля сотрудника','class'=>'modalButton'],
                            ],
                            [
                                'label' => 'Посмотреть профиль',
                                'url' => '#',
                                'linkOptions' => ['value'=> Url::to(['/employee/view', 'id' => $model['user_id']]), 'title' => 'Просмотр профиля сотрудника','class'=>'modalButton'],
                            ],

                            [
                                'label' => 'Изменить пароль',
                                'url' => '#',
                                'linkOptions' => ['value'=> Url::to(['/employee/change-pass', 'id' => $model['user_id']]), 'title' => 'Изменить пароль','class'=>'modalButton'],
                            ],
                            [
                                'label' => ($model['status'] == User::STATUS_ACTIVE ? 'Деактивировать пользователя' : 'Активировать пользователя'),
                                'url' => ($model['status'] == User::STATUS_ACTIVE ? Url::to(['/employee/deactivate', 'id' => $model['id']]) : Url::to(['/employee/activate', 'id' => $model['id']])),
                                'linkOptions' => [
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Вы действительно хотите '.($model['status'] == User::STATUS_ACTIVE ? 'деактивировать' : 'активировать').' сотруника?'),
                                        'method' => 'post',
                                    ],
                                ]
                            ],
                            [
                                'label' => 'Удалить пользователя',
                                'url' => ['/employee/delete-user', 'id' => $model['id']],
                                'linkOptions' => [
                                    'data' => [
                                        'confirm' => Yii::t('app', 'Вы действительно хотите удалить сотруника со всеми его данными?'),
                                        'method' => 'post',
                                    ],
                                ]
                            ],
                        ],
                    ],
                ]).'</div>';
            },
        ],
        'template' => '{all}'
    ];
}
else {
    $actionColumn = [
        'class' => 'kartik\grid\ActionColumn',
        'hiddenFromExport' => true,
        'contentOptions' => [
            'style' => 'width:80px;  min-width:80px; max-width:80px;'
        ],

        'buttons' => [
            'all' => function ($url, $model) {
                return '<div class="btn-group">'.ButtonDropdown::widget([
                    'label' => '...',
                    'options' => ['class' => 'btn btn-default dropleft'],
                    'dropdown' => [
                        'options' => ['class' => 'dropdown-menu'],
                        'items' => [
                            [
                                'label' => 'Посмотреть профиль',
                                'url' => '#',
                                'linkOptions' => ['value'=> Url::to(['/employee/view', 'id' => $model['user_id']]), 'title' => 'Просмотр профиля сотрудника','class'=>'modalButton'],
                            ],
                        ],
                    ],
                ]).'</div>';
            },
        ],
        'template' => '{all}'
    ];
};

$columns = [
    [
        'attribute' => 'username',
        'format' => 'raw',
        'value' => function ($data) {
            return $data['username'];
        },
        'contentOptions' => [
            'style' => 'white-space: normal;width:15%;'
        ],
    ],
    [
        'attribute' => 'fio',
        'format' => 'raw',
        'value' => function ($data) {
            return $data['fio'];
        },
        'contentOptions' => [
            'style' => 'white-space: normal;'
        ],
    ],

    [
        'attribute' => 'phone',
        'format' => 'raw',
        'value' => function ($data) {
            return $data['phone'];
        },
        'contentOptions' => [
            'style' => 'white-space: normal;width:15%; text-align:center;'
        ],
    ],
    [
        'attribute' => 'role',
        'format' => 'raw',
        'value' => function ($data) {
            if ($data['roles']) {
                return AuthHelper::getRoles()[$data['roles']];
            }
            return null;
        },
        'contentOptions' => [
            'style' => 'white-space: normal;width:15%; text-align:center'
        ],
    ],
    [
        'attribute' => 'status',
        'format' => 'raw',
        'value' => function ($data) {
            return User::statusName($data['status']);
        },
        'contentOptions' => [
            'style' => 'white-space: normal;width:10%; text-align:center'
        ],
    ],
    $actionColumn
];

//echo '<pre>'.print_r($searchModel, true).'</pre>';
?>
<div class="flex-main-content">
    <h3><?= Html::encode($this->title) ?></h3>
    <p>
        <?php if ($user->can('rl_admin') or $user->can('rl_hl_user')):?>
            <?= Html::button(Yii::t('app', 'Добавить сотрудника'), ['value'=>Url::to('/employee/create'), 'title' => 'Добавить сотрудника', 'class' => 'btn btn-ppu btn-success modalButton']) ?>
        <?php endif?>
        <?= Html::a('Очистить фильтр', ['/employee/clear-filter'], ['class' => 'btn btn-ppu btn-danger']) ?>
    </p>
    <?php
    Modal::begin([
        'title'=>'<h3 id="modalHeader"></h3>',
        'id'=>'modal',
        'size'=>'modal-lg',
    ]);
    echo "<div id='modalContent'></div>";
    Modal::end();
    ?>

    <div class="text-right">
        <div class="btn-group">
            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                Показать <?=$dataProvider->pagination->pageSize?> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu btn-dropdown">
                <li><?= Html::a(20, Url::current(['per-page' => 20]), ['class' => 'dropdown-item'])?></li>
                <li><?= Html::a(50, Url::current(['per-page' => 50]), ['class' => 'dropdown-item'])?></li>
                <li><?= Html::a(100, Url::current(['per-page' => 100]), ['class' => 'dropdown-item'])?></li>
            </ul>
        </div>
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'responsive' => false,
        'pager' => [
            'class' => '\common\widgets\LinkPagerWalive',
        ],
        'options' => ['class' => 'table-sm'],
        'columns' => $columns,
    ]); ?>
</div>
