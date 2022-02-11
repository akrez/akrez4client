<?php

use app\assets\cdn\ICheckAsset;
use app\models\Blog;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;

$isEditMode = (isset($isEditMode) && $isEditMode);
$isSelectMode = (isset($isSelectMode) && $isSelectMode);

$selectedDeliveryId = (isset($selectedDeliveryId) ? $selectedDeliveryId : null);

$sampleModel = new app\models\Model();
$dataProvider = new ArrayDataProvider([
    'allModels' => $allModels,
    'modelClass' => 'app\models\Model',
    'sort' => false,
    'pagination' => false,
]);

echo GridView::widget([
    'layout' => "{items}\n{pager}",
    'dataProvider' => $dataProvider,
    'headerRowOptions' => [
        'class' => 'thead-dark',
    ],
    'tableOptions' => [
        'class' => 'table table-striped table-bordered table-hover table-sm table-vertical-align-middle',
    ],
    'columns' => [
        [
            'class' => 'yii\grid\RadioButtonColumn',
            'name' => 'delivery_id',
            'radioOptions' => function ($model) use ($selectedDeliveryId) {
                return [
                    'value' => (isset($model['id']) ? $model['id'] : null),
                    'checked' => (isset($model['id']) and $model['id'] == $selectedDeliveryId),
                    'class' => 'icheck-inline'
                ];
            },
            'visible' => $isSelectMode,
        ],
        'name',
        [
            'attribute' => 'phone',
            'format' => 'raw',
            'value' => function ($model, $key, $index, $grid) {
                return $model['phone'] . "<br>" . $model['mobile'];
            },
        ],
        [
            'attribute' => 'city',
            'format' => 'raw',
            'value' => function ($model, $key, $index, $grid) use ($sampleModel) {
                return Blog::getApiConstant(Blog::print('language'), ['city', $model['city']]) . " | " . $model['address'] .
                    "<br>" .
                    $sampleModel->getAttributeLabel('postal_code') . " | " . $model['postal_code'] .
                    "<br>" .
                    "<small>" . $model['des'] . "</small>";
            },
        ],
        [
            'label' => '',
            'format' => 'raw',
            'value' => function ($model, $key, $index, $grid) {
                return '<a class="btn btn-primary btn-block btn-social btn-sm" href="' . Blog::url('site/delivery-edit', ['id' => $model['id']]) . '" >' .
                    '<i class="far fa-eye"></i></i>' .
                    Yii::t('app', 'Edit') .
                    '</a>' .
                    '<a class="btn btn-danger btn-block btn-social btn-sm" href="' . Blog::url('site/delivery-delete', ['id' => $model['id']]) . '" data-confirm="' . Yii::t('yii', 'Are you sure you want to delete this item?') . '">' .
                    '<i class="fas fa-trash fa-2x"></i>' .
                    Yii::t('app', 'Delete') .
                    '</a>';
            },
            'visible' => $isEditMode,
        ],
    ],
]);
