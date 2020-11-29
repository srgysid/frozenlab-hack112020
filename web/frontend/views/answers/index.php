<?php

use kartik\grid\GridView;
use backend\assets\AppAsset;
use yii\bootstrap4\ButtonDropdown;
use yii\bootstrap4\Modal;
use kartik\select2\Select2;
use yii\helpers\Html;
use yii\helpers\Url;
use common\models\Answers;

AppAsset::register($this);

$this->title = Yii::t('app', 'Ответы');
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user;

if ($user->can('rl_admin') or $user->can('rl_key_user') or $user->can('rl_view_user')) {
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
                                    'linkOptions' => ['value'=> Url::to(['/answers/update', 'id' => $model['id']]), 'title' => 'Редактирование','class'=>'modalButton'],
                                ],
                                [
                                    'label' => 'Удалить',
                                    'url' => '/answers/delete?id='.$model['id'],
                                    'linkOptions' => [
                                        'data' => [
                                            'confirm' => Yii::t('app', 'Вы действительно хотите удалить ответ?'),
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
    $actionColumn = null;
};

$columns = [
    [
        'format' => 'raw',
        'attribute' => 'orders_short_desc',
        'value' => function ($data) {
            return $data['orders_short_desc'];
        },
        'contentOptions' => [
            'style' => 'white-space: normal;text-align:left;'
        ],
    ],
    [
        'format' => 'raw',
        'attribute' => 'orders_creator_name',
        'filter' => Select2::widget([
            'theme' => Select2::THEME_DEFAULT,
            'model' => $searchModel,
            'attribute' => 'creator_ids',
            'value' => $searchModel['creator_ids'],
            'data' => $cards,
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
            return $data['orders_creator_name'];
        },
        'contentOptions' => [
            'style' => 'white-space: normal;text-align:center;width:25%;'
        ],
    ],
    [
        'format' => 'raw',
        'attribute' => 'full_desc',
        'value' => function ($data) {
            return $data['full_desc'];
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
            return Answers::getStatus()[$data['status_id']];
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
        <?php if ($user->can('rl_admin') or $user->can('rl_key_user') or $user->can('rl_view_user')):?>
            <?= Html::button(Yii::t('app', 'Добавить ответ'), ['value'=>Url::to('/answers/create'), 'title' => 'Добавить ответ', 'class' => 'btn btn-ppu btn-success modalButton']) ?>
        <?php endif?>
        <?= Html::a('Очистить фильтр', ['/answers/clear-filter'], ['class' => 'btn btn-ppu btn-danger']) ?>
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
