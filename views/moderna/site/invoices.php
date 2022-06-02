<?php

use app\models\Blog;
use app\models\Model;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\LinkPager;
use yii\data\ArrayDataProvider;
use yii\data\Pagination;
use yii\grid\GridView;
use yii\helpers\HtmlPurifier;

$this->title = Yii::t('app', 'Invoices');
$sampleModel = new Model();
$hasInvoices = boolval(Blog::getData('invoices'));

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

<?php if ($hasInvoices) :
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
                'allModels' => Blog::getData('invoices'),
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
                        [
                            'attribute' => 'invoice.id',
                            'label' => $sampleModel->getAttributeLabel('id'),
                        ],
                        [
                            'attribute' => 'invoice.status',
                            'label' => $sampleModel->getAttributeLabel('status'),
                            'value' => function ($model, $key, $index, $grid) {
                                if (isset($model['invoice']['status'])) {
                                    return Blog::getConstant('invoice_valid_statuses', $model['invoice']['status']);
                                }
                                return '';
                            },
                        ],
                        [
                            'attribute' => 'invoice.updated_at',
                            'format' => 'datetimefa',
                            'label' => $sampleModel->getAttributeLabel('updated_at'),
                        ],
                        [
                            'format' => 'raw',
                            'label' => $sampleModel->getAttributeLabel('address'),
                            'value' => function ($model, $key, $index, $grid) use ($sampleModel) {
                                foreach ($model['deliveries'] as $delivery) {
                                    if ($delivery['id'] == $model['invoice']['delivery_id']) {
                                        return Blog::getConstant('city', $delivery['city']) . " | " . HtmlPurifier::process($delivery['address']);
                                    }
                                }
                            },

                        ],
                        [
                            'attribute' => 'invoice.price',
                            'format' => 'price',
                            'label' => $sampleModel->getAttributeLabel('price'),
                        ],
                        [
                            'attribute' => 'invoice.carts_count',
                            'label' => $sampleModel->getAttributeLabel('carts_count'),
                        ],
                        [
                            'label' => '',
                            'format' => 'raw',
                            'value' => function ($model, $key, $index, $grid) {
                                return '<a class="btn btn-primary btn-block btn-social" href="' . Blog::url('site/invoice-view', ['id' => $model['invoice']['id']]) . '" >' .
                                    '<i class="far fa-eye"></i></i>' .
                                    Yii::t('app', 'View invoice') .
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