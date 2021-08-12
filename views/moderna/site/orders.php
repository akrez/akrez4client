<?php

use app\models\Blog;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\LinkPager;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use yii\grid\GridView;

$this->title = Yii::t('app', 'Orders');
$hasOrders = boolval(Blog::getData('orders'));

$this->registerCss("
.table-vertical-align-middle td,
.table-vertical-align-middle thead th {
    vertical-align: middle;
}
");
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

<?php if ($hasOrders) :
    $pagination = new Pagination([
        'pageSizeParam' => 'page_size',
        'pageSize' => Blog::getData('pagination', 'page_size'),
        'page' => Blog::getData('pagination', 'page'),
        'totalCount' => Blog::getData('pagination', 'total_count'),
    ]);
?>
    <div class="row">
        <div class="col-sm-12">
            <?php
            $dataProvider = new ArrayDataProvider([
                'allModels' => Blog::getData('orders'),
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
                        'id',
                        'price:price',
                        'carts_count',
                        'updated_at:datetimefa',
                        'name',
                        'phone',
                        'mobile',
                        [
                            'attribute' => 'city',
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $grid) {
                                return Blog::getApiConstant(Blog::print('language'), ['city', $model['city']]);
                            },
                        ],
                        [
                            'label' => '',
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $grid) {
                                return '<a class="btn btn-primary btn-block btn-social" href="' . Blog::url('site/order-view', ['id' => $model['id']]) . '" >' .
                                    '<i class="far fa-eye"></i></i>' .
                                    Yii::t('app', 'View order') .
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