<?php

use app\models\Blog;
use yii\helpers\HtmlPurifier;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Html;
use yii\web\View;

$secureTitle = HtmlPurifier::process(Blog::getData('product', 'title'));

$this->title = $secureTitle;

$hasImages = (count(Blog::getData('images')) > 0);

$this->registerJs('
function changePackageCnt(sender, cnt) {
    var input = $(sender).closest(".card-body").find("input").first();
    var newValue = (parseInt(input.val()) || 0) + cnt;
    if (newValue < 1) {
        newValue = 1;
    }
    input.val(newValue);
}
', View::POS_HEAD);
$this->registerCss("
.h6, h6 {
    font-size: .75rem;
}
@media (min-width: 576px) {
    .card-columns {
        -webkit-column-count: 1;
        -moz-column-count: 1;
        column-count: 1;
    }
}
@media (min-width: 768px) {
    .card-columns {
        -webkit-column-count: 2;
        -moz-column-count: 2;
        column-count: 2;
    }
}
@media (min-width: 992px) {
    .card-columns {
        -webkit-column-count: 3;
        -moz-column-count: 3;
        column-count: 3;
    }
}
@media (min-width: 1200px) {
    .card-columns {
        -webkit-column-count: 4;
        -moz-column-count: 4;
        column-count: 4;
    }
}
.row-flex {
    display: flex;
    flex-wrap: wrap;
}
.row-flex div[class*='col-'] {
    height: 100%;
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
        [
            'label' => Blog::categories(Blog::getData('categoryId')),
            'url' => Blog::url('site/category', ['id' => Blog::getData('categoryId')]),
        ],
        [
            'label' => $secureTitle,
        ],
    ],
]);
?>
<div class="row">
    <div class="col-sm-12 pb-2">
        <h1><?= $secureTitle ?></h1>
    </div>
</div>
<div class="row pb-2">
    <?php if ($hasImages) : ?>
        <div class="col-sm-5 pb-2">
            <div class="row">
                <div class="col-12">
                    <div id="carouselExampleIndicators" class="carousel slide carousel-fade rounded-lg" data-ride="carousel">
                        <div class="carousel-inner">
                            <?php
                            $i = 0;
                            foreach (Blog::getData('images') as $imageKey => $image) :
                                echo '<div class="carousel-item ' . ($i == 0 ? 'active' : '') . '"> <img src="' . Blog::getImage('product', '400', $image['name']) . '" class="rounded d-block w-100" alt="' . $secureTitle . '"> </div>';
                                $i++;
                            endforeach;
                            ?>
                        </div>
                    </div>
                </div>
            </div>
            <?php if (count(Blog::getData('images')) > 1) : ?>
                <div class="row row-flex">
                    <?php
                    $i = 0;
                    foreach (Blog::getData('images') as $imageKey => $image) :
                        echo '<div class="col-sm-4 mt-1"> <img style="cursor: pointer;" onclick="$(' . "'#carouselExampleIndicators'" . ').carousel(' . $i . ')" src="' . Blog::getImage('product', '400', $image['name']) . '" class="rounded w-100" alt="' . $secureTitle . '"> </div>';
                        $i++;
                    endforeach;
                    ?>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>
    <div class="<?= $hasImages ? 'col-sm-7' : 'col-sm-12' ?>">
        <table class="table table-striped table-bordered table-hover table-sm">
            <tbody>
                <?php foreach (Blog::getData('product', '_fields') as $productTitle => $productField) : ?>
                    <tr>
                        <td>
                            <strong> <?= HtmlPurifier::process($productTitle) ?> </strong>
                        </td>
                        <td>
                            <?php
                            echo HtmlPurifier::process(implode(' ,', $productField['values'])) . ' ' . HtmlPurifier::process($productField['unit']);
                            ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<div class="row pb-2">
    <div class="col-sm-12">
        <div class="card-columns">
            <?php foreach (Blog::getData('packages') as $package) : ?>
                <div class="card">
                    <?php
                    echo Html::beginForm(Blog::url('site/basket-add'), 'get');
                    echo Html::hiddenInput('package_id', $package['id']);
                    ?>
                    <div class="card-body p-2">
                        <h6 class="card-text"><?= Yii::t('app', 'Guaranty') ?> <?= HtmlPurifier::process($package['guaranty']) ?></h6>
                        <?php if ($package['des']) : ?>
                            <small class="mb-1 text-justify">
                                <?= HtmlPurifier::process($package['des']) ?>
                            </small>
                        <?php endif; ?>
                        <?php if ($package['color_code']) : ?>
                            <div class="card-text">
                                <small class="mb-1 text-justify">
                                    <span class="border border-dark rounded" style="background-color: <?= $package['color_code'] ?>">⠀⠀</span>
                                    <?= Blog::colorLabel($package['color_code']) ?>
                                </small>
                            </div>
                        <?php endif; ?>
                        <div class="card-text text-left">
                            <small class="mb-1 text-justify">
                                <?= HtmlPurifier::process(Yii::$app->formatter->asPrice($package['price'])) ?>
                            </small>
                        </div>
                    </div>
                    <div class="card-body pr-2 pl-2 pt-0 pb-0">
                        <div class="input-group input-group-sm">
                            <div class="input-group-prepend">
                                <div class="btn btn-danger" onclick="changePackageCnt(this, -1)">
                                    <i class="fa fa-minus" aria-hidden="true"></i>
                                </div>
                            </div>
                            <input name="cnt" type="text" value="1" class="form-control text-center">
                            <div class="input-group-append">
                                <div class="btn btn-success" onclick="changePackageCnt(this, +1)">
                                    <i class="fa fa-plus" aria-hidden="true"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body pr-2 pl-2 pt-1 pb-2">
                        <button class="btn btn-sm btn-primary btn-social btn-block text-center">
                            <i class="fa fa-cart-plus" aria-hidden="true"></i>
                            <?= Yii::t('app', 'Add to basket') ?>
                        </button>
                    </div>
                    <?= Html::endForm() ?>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>
<div class="row pb-2">
    <div class="col-sm-12">
        <?= HtmlPurifier::process($page) ?>
    </div>
</div>