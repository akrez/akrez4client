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

<div class="row mb-2">
    <div class="col-sm-12">

        <a class="btn btn-success btn-social" href="<?= Blog::url('site/delivery-add') ?>">
            <i class="fa fa-plus"></i></i>
            <?= Yii::t('app', 'Add delivery') ?>
        </a>

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
            <div class="table-responsive">
                <?= $this->render('_delivery_table', [
                    'allModels' => Blog::getData('deliveries'),
                    'isEditMode' => true,
                ]) ?>
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