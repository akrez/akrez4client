<?php

use app\models\Blog;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\LinkPager;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use yii\grid\GridView;

$this->title = Yii::t('app', 'Deliveries');
$hasDeliveries = boolval(Blog::getData('deliveries'));

?>

<?=
Breadcrumbs::widget([
    'homeLink' => [
        'label' => Yii::t('yii', 'Home'),
        'url' => Blog::firstPageUrl(),
    ],
    'links' => [
        ['label' => $this->title],
    ],
]);
?>

<div class="row">
    <div class="col-sm-12 pb-2">
        <h1><?= $this->title ?></h1>
    </div>
</div>

<?php if ($hasDeliveries) :
    $pagination = new Pagination([
        'pageSizeParam' => 'page_size',
        'pageSize' => Blog::getData('pagination', 'page_size'),
        'page' => Blog::getData('pagination', 'page'),
        'totalCount' => Blog::getData('pagination', 'total_count'),
    ]);
?>
    <div class="row">
        <div class="col-sm-12">

            <a class="btn btn-success btn-social" href="<?= Blog::url('site/delivery-add') ?>">
                <i class="fa fa-plus"></i></i>
                <?= Yii::t('app', 'Add delivery') ?>
            </a>

            <?php
            $sampleModel = new app\models\Model();
            $dataProvider = new ArrayDataProvider([
                'allModels' => Blog::getData('deliveries'),
                'modelClass' => 'app\models\Model',
                'sort' => false,
                'pagination' => false,
            ]);
            ?>

            <div class="table-responsive">
                <?=
                GridView::widget([
                    'dataProvider' => $dataProvider,
                    'headerRowOptions' => [
                        'class' => 'thead-dark',
                    ],
                    'tableOptions' => [
                        'class' => 'table table-striped table-bordered table-hover table-sm table-vertical-align-middle',
                    ],
                    'columns' => [
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
                        ],
                    ],
                ]);
                ?>
            </div>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-sm-12">
            <?= LinkPager::widget([
                'pagination' => $pagination,
                'hideOnSinglePage' => true,
                'disableCurrentPageButton' => true,
            ]); ?>
        </div>
    </div>
<?php else : ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="alert alert-warning" role="alert">
                <?= Yii::t('yii', 'No results found.'); ?>
            </div>
        </div>
    </div>
<?php endif; ?>