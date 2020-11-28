<?php

use kartik\grid\GridView;
use backend\assets\AppAsset;
use yii\bootstrap4\ButtonDropdown;
use yii\bootstrap4\Modal;
use yii\helpers\Html;
use yii\helpers\Url;

AppAsset::register($this);

$this->title = Yii::t('app', 'Подразделения');
$this->params['breadcrumbs'][] = $this->title;

$user = Yii::$app->user;

if ($user->can('rl_admin') or $user->can('rl_key_user')) {
    $columns = [
        [
            'format' => 'raw',
            'attribute' => 'code',
            'value' => function ($data) {
                return $data['code'];
            },
            'contentOptions' => [
                'style' => 'white-space: normal;width:10%; text-align:center;'
            ],
        ],
        [
            'format' => 'raw',
            'attribute' => 'name',
            'value' => function ($data) {
                return $data['name'];
            },
            'contentOptions' => [
                'style' => 'white-space: normal;width:25%; text-align:center;'
            ],
        ],
        [
            'format' => 'raw',
            'attribute' => 'short_name',
            'value' => function ($data) {
                return $data['short_name'];
            },
            'contentOptions' => [
                'style' => 'white-space: normal;width:25%; text-align:center;'
            ],
        ],
        [
            'format' => 'raw',
            'attribute' => 'full_name',
            'value' => function ($data) {
                return $data['full_name'];
            },
            'contentOptions' => [
                'style' => 'white-space: normal;'
            ],
        ],
        [
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
                                    'linkOptions' => ['value'=> Url::to(['/department/update', 'id' => $model['id']]), 'title' => 'Редактирование','class'=>'modalButton'],
                                ],
                            ],
                        ],
                    ]).'</div>';

                },
            ],
            'template' => '{all}'
        ],
    ];
}
else {
    $columns = [
        [
            'format' => 'raw',
            'attribute' => 'code',
            'value' => function ($data) {
                return $data['code'];
            },
            'contentOptions' => [
                'style' => 'white-space: normal;width:10%; text-align:center;'
            ],
        ],
        [
            'format' => 'raw',
            'attribute' => 'name',
            'value' => function ($data) {
                return $data['name'];
            },
            'contentOptions' => [
                'style' => 'white-space: normal;width:25%; text-align:center;'
            ],
        ],
        [
            'format' => 'raw',
            'attribute' => 'short_name',
            'value' => function ($data) {
                return $data['short_name'];
            },
            'contentOptions' => [
                'style' => 'white-space: normal;width:25%; text-align:center;'
            ],
        ],
        [
            'format' => 'raw',
            'attribute' => 'full_name',
            'value' => function ($data) {
                return $data['full_name'];
            },
            'contentOptions' => [
                'style' => 'white-space: normal;'
            ],
        ],
    ];
};

//echo '<pre>'.print_r($dataProvider->pagination, true).'</pre>';
?>
<div class="flex-main-content">
    <h3><?= Html::encode($this->title) ?></h3>
    <p>
        <?= Html::a('Очистить фильтр', ['/department/clear-filter'], ['class' => 'btn btn-ppu btn-danger']) ?>
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
                <li><?= Html::a(20, Url::current(['per-page'=>20]), ['class' => 'dropdown-item'])?></li>
                <li><?= Html::a(50, Url::current(['per-page'=>50]), ['class' => 'dropdown-item'])?></li>
                <li><?= Html::a(100, Url::current(['per-page'=>100]), ['class' => 'dropdown-item'])?></li>
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
