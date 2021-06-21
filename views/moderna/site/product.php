<?php

use app\models\Blog;
use yii\helpers\HtmlPurifier;
use yii\bootstrap4\Breadcrumbs;

$secureTitle = HtmlPurifier::process(Blog::getData('product', 'title'));

$this->title = $secureTitle;

$hasImages = (count(Blog::getData('images')) > 0);

$this->registerCss("
.h6, h6 {
    font-size: .75rem;
}
.carousel-indicators li {
    color: gray;
}
.carousel-control-next {
    background-image: -webkit-linear-gradient(left,rgba(0,0,0,.2) 0,rgba(0,0,0,.0001) 100%);
    background-image: -o-linear-gradient(left,rgba(0,0,0,.2) 0,rgba(0,0,0,.0001) 100%);
    background-image: -webkit-gradient(linear,left top,right top,from(rgba(0,0,0,.2)),to(rgba(0,0,0,.0001)));
    background-image: linear-gradient(to right,rgba(0,0,0,.2) 0,rgba(0,0,0,.0001) 100%);
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#80000000', endColorstr='#00000000', GradientType=1);
    background-repeat: repeat-x;
}
.carousel-control-prev {
    right: 0;
    left: auto;
    background-image: -webkit-linear-gradient(left,rgba(0,0,0,.0001) 0,rgba(0,0,0,.2) 100%);
    background-image: -o-linear-gradient(left,rgba(0,0,0,.0001) 0,rgba(0,0,0,.2) 100%);
    background-image: -webkit-gradient(linear,left top,right top,from(rgba(0,0,0,.0001)),to(rgba(0,0,0,.2)));
    background-image: linear-gradient(to right,rgba(0,0,0,.0001) 0,rgba(0,0,0,.2) 100%);
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#00000000', endColorstr='#80000000', GradientType=1);
    background-repeat: repeat-x;
}
.carousel-control-next:focus, .carousel-control-next:hover {
    color: #fff;
    text-decoration: none;
    outline: 0;
    filter: alpha(opacity=90);
    opacity: .9;
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
            <div id="carouselExampleIndicators" class="carousel slide carousel-fade border rounded-lg" data-ride="carousel">
                <div class="carousel-inner">
                    <?php
                    $i = 0;
                    foreach (Blog::getData('images') as $imageKey => $image) :
                        echo '<div class="carousel-item ' . ($i == 0 ? 'active' : '') . '"> <img src="' . Blog::getImage('product', '400', $image['name']) . '" class="d-block w-100" alt="' . $secureTitle . '"> </div>';
                        $i++;
                    endforeach;
                    ?>
                </div>
                <?php if (count(Blog::getData('images')) > 1) : ?>
                    <a class="carousel-control-prev" href="#carouselExampleIndicators" role="button" data-slide="prev">
                        <span class="carousel-control-prev-icon" aria-hidden="true"></span>
                        <span class="sr-only">Previous</span>
                    </a>
                    <a class="carousel-control-next" href="#carouselExampleIndicators" role="button" data-slide="next">
                        <span class="carousel-control-next-icon" aria-hidden="true"></span>
                        <span class="sr-only">Next</span>
                    </a>
                <?php endif; ?>
            </div>
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
                    <div class="card-body p-2">
                        <h6 class="card-text"><?= Yii::t('app', 'Guaranty') ?>: <?= HtmlPurifier::process($package['guaranty']) ?></h6>
                        <?php if ($package['des']) : ?>
                            <small class="mb-1 text-justify">
                                <?= HtmlPurifier::process($package['des']) ?>
                            </small>
                        <?php endif; ?>
                        <?php if ($package['color']) : ?>
                            <div class="card-text">
                                <small class="mb-1 text-justify">
                                    <span class="border border-dark rounded" style="background-color: #<?= $package['color'] ?>">⠀⠀</span>
                                    <?= Blog::getConstant('color', $package['color']) ?>
                                </small>
                            </div>
                        <?php endif; ?>
                        <div class="card-text text-left">
                            <small class="mb-1 text-justify">
                                <?= HtmlPurifier::process(Yii::$app->formatter->asPrice($package['price'])) ?>
                            </small>
                        </div>
                    </div>
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