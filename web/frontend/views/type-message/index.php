<?php

use kartik\date\DatePicker;
use kartik\grid\GridView;
use kartik\select2\Select2;
use frontend\assets\AppAsset;
use yii\bootstrap4\ButtonDropdown;
use yii\bootstrap4\Modal;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;

AppAsset::register($this);

$this->title = Yii::t('app', 'Сообщения по видам поручений');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="flex-main-content">
    <h3><?= Html::encode($this->title) ?></h3>
    <p>
        <?= Html::button(Yii::t('app', 'Добавить сообщение'), ['value'=>Url::to('/type-message/create'), 'title' => 'Добавить сообщение', 'class' => 'btn btn-ppu btn-success modalButton']) ?>
        <?= Html::a('Очистить фильтр', ['/type-message/clear-filter'], ['class' => 'btn btn-ppu btn-danger']) ?>
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
                Показать <?=$dataProvider->pagination->pageSize?><span class="caret"></span>
            </button>
            <ul class="dropdown-menu exp-btn">
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
        'columns' => [
            [
                'attribute' => 'type_order_name',
                'filter' => Select2::widget([
                    'theme' => Select2::THEME_DEFAULT,
                    'model' => $searchModel,
                    'attribute' => 'type_order_ids',
                    'value' => $searchModel['type_order_ids'],
                    'data' => $typeOrder,
                    'options' => [
                        'placeholder' => Yii::t('app', 'Вид поручения ...'),
                        'multiple' => true,
                        'class' => 'label-warning'

                    ],
                    'pluginOptions' => [
                        'allowClear' => true,
                    ],
                ]),
                'format' => 'raw',
                'value' => function ($data) {
                    return $data['type_order_name'];
                },
                'contentOptions' => [
                    'style' => 'white-space: normal;width:45%;'
                ],
            ],
            [
                'format' => 'ntext',
                'attribute' => 'name',
                'value' => function ($data) {
                    return $data['name'];
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
                                        'linkOptions' => ['value'=> Url::to(['/type-message/update', 'id' => $model['id']]), 'title' => 'Редактирование сообщения ','class'=>'modalButton'],
                                    ],
                                    [
                                        'label' => 'Удалить',
                                        'url' => '/type-message/delete?id='.$model['id'],
                                        'linkOptions' => [
                                            'data' => [
                                                'confirm' => Yii::t('app', 'Вы действительно хотите удалить сообщение: {0}?', $model['name']),
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
            ],
        ],
    ]); ?>
</div>
