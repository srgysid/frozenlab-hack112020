<?php

use kartik\grid\GridView;
use backend\assets\AppAsset;
use yii\bootstrap4\ButtonDropdown;
use yii\bootstrap4\Modal;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Orders;

AppAsset::register($this);

$this->title = Yii::t('app', 'Распоряжения');
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user;

if ($user->can('rl_admin') or $user->can('rl_key_user') or $user->can('rl_chief')) {
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
                                    'label' => 'Редактировать',
                                    'url' => '#',
                                    'linkOptions' => ['value'=> Url::to(['/orders/update', 'id' => $model['id']]), 'title' => 'Редактирование','class'=>'modalButton'],
                                ],
                                [
                                    'label' => 'Переназначить подразделение',
                                    'url' => '#',
                                    'linkOptions' => ['value'=> Url::to(['/orders/change-department', 'id' => $model['id']]), 'title' => 'Выбор нового подразделения','class'=>'modalButton'],
                                ],
                                [
                                    'label' => 'Ответы исполнителей',
                                    'url' => '/answers/index-order?orders_id='.$model['id'],
                                ],

                                [
                                    'label' => 'Просмотреть',
                                    'url' => '#',
                                    'linkOptions' => ['value'=> Url::to(['/orders/view', 'id' => $model['id']]), 'title' => 'Просмотр','class'=>'modalButton'],
                                ],
                                [
                                    'label' => 'Удалить',
                                    'url' => '/orders/delete?id='.$model['id'],
                                    'linkOptions' => [
                                        'data' => [
                                            'confirm' => Yii::t('app', 'Вы действительно хотите удалить карточку?'),
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
                                'label' => 'Просмотреть',
                                'url' => '#',
                                'linkOptions' => ['value'=> Url::to(['/orders/view', 'id' => $model['id']]), 'title' => 'Просмотр','class'=>'modalButton'],
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
        'format' => 'raw',
        'attribute' => 'created_at',
        'value' => function ($data) {
            return Yii::$app->formatter->asDate($data['created_at']).' '.Yii::$app->formatter->asTime($data['created_at'], 'php:H:i');
        },
        'contentOptions' => [
            'style' => 'white-space: normal;width:10%; text-align:center;'
        ],
    ],
    [
        'format' => 'raw',
        'attribute' => 'type_cards',
        'filter' => Select2::widget([
            'theme' => Select2::THEME_DEFAULT,
            'model' => $searchModel,
            'attribute' => 'type_cards_ids',
            'value' => $searchModel['type_cards_ids'],
            'data' => $type_cards,
            'options' => [
                'placeholder' => Yii::t('app', 'Выберите ...'),
                'multiple' => true,
                'class' => 'label-warning'
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ]),
        'value' => function ($data) {
            return Orders::getTypeCards()[$data['type_cards']];
        },
        'contentOptions' => [
            'style' => 'white-space: normal;text-align:center;'
        ],
    ],
    [
        'format' => 'raw',
        'attribute' => 'priority',
        'filter' => Select2::widget([
            'theme' => Select2::THEME_DEFAULT,
            'model' => $searchModel,
            'attribute' => 'priority_ids',
            'value' => $searchModel['priority_ids'],
            'data' => $priority,
            'options' => [
                'placeholder' => Yii::t('app', 'Выберите ...'),
                'multiple' => true,
                'class' => 'label-warning'
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ]),
        'value' => function ($data) {
            return Orders::getPriority()[$data['priority']];
        },
        'contentOptions' => [
            'style' => 'white-space: normal;width:10%;text-align:center;'
        ],
    ],

    [
        'format' => 'raw',
        'attribute' => 'department_name',
        'filter' => Select2::widget([
            'theme' => Select2::THEME_DEFAULT,
            'model' => $searchModel,
            'attribute' => 'department_ids',
            'value' => $searchModel['department_ids'],
            'data' => $departments,
            'options' => [
                'placeholder' => Yii::t('app', 'Выберите ...'),
                'multiple' => true,
                'class' => 'label-warning'
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ]),
        'value' => function ($data) {
            return $data['department_name'];
        },
        'contentOptions' => [
            'style' => 'white-space: normal;width:15%; text-align:center;'
        ],
    ],
    [
        'format' => 'raw',
        'attribute' => 'short_desc',
        'value' => function ($data) {
            return $data['short_desc'];
        },
        'contentOptions' => [
            'style' => 'white-space: normal;width:25%; text-align:left;'
        ],
    ],
    [
        'format' => 'raw',
        'attribute' => 'status_id',
        'filter' => Select2::widget([
            'theme' => Select2::THEME_DEFAULT,
            'model' => $searchModel,
            'attribute' => 'status_ids',
            'value' => $searchModel['status_ids'],
            'data' => $status,
            'options' => [
                'placeholder' => Yii::t('app', 'Выберите ...'),
                'multiple' => true,
                'class' => 'label-warning'
            ],
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ]),
        'value' => function ($data) {
            return Orders::getStatus()[$data['status_id']];
        },
        'contentOptions' => [
            'style' => 'white-space: normal;text-align:center;width:10%;'
        ],
    ],

    $actionColumn
];

//echo '<pre>'.print_r($test, true).'</pre>';
?>
<style>
    .modal-lg{
        max-width: 1100px;
    }
</style>

<div class="flex-main-content">
    <h3><?= Html::encode($this->title) ?></h3>
    <p>
        <?php if ($user->can('rl_admin') or $user->can('rl_key_user') or $user->can('rl_chief')):?>
            <?= Html::button(Yii::t('app', 'Добавить карточку'), ['value'=>Url::to('/orders/create'), 'title' => 'Добавить карточку', 'class' => 'btn btn-ppu btn-success modalButton']) ?>
        <?php endif?>
        <?= Html::a('Очистить фильтр', ['/orders/clear-filter'], ['class' => 'btn btn-ppu btn-danger']) ?>
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
