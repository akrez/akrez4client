<?php

use app\models\Blog;
use app\models\FieldList;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
use yii\bootstrap4\LinkPager;

$pagination = new Pagination([
    'pageSizeParam' => 'page_size',
    'pageSize' => Blog::getData('pagination', 'page_size'),
    'page' => Blog::getData('pagination', 'page'),
    'totalCount' => Blog::getData('pagination', 'total_count'),
]);

$this->registerCss("

.card {
    color: #000000;
}
a.card {
    text-decoration: none;
}

.card img {
    text-decoration: none;
}

.card .caption * {
    margin: 9px 0 0;
}

.card .caption h5 {
    font-weight: bold;
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
        -webkit-column-count: 3;
        -moz-column-count: 3;
        column-count: 3;
    }
}
");
?>

<?php if (count((array)Blog::getData('products')) > 0) : ?>

    <div class="row pt-2">
        <div class="col-sm-12">
            <ul class="pagination">
                <?php foreach (Blog::getData('sort', 'attributes') as $sortAttributeId => $sortAttributeValue) : ?>
                    <?php if (Blog::getData('sort', 'attribute') == $sortAttributeId) : ?>
                        <li class="page-item active"><a class="page-link" href="#"><?= HtmlPurifier::process($sortAttributeValue) ?></a></li>
                    <?php else : ?>
                        <li class="page-item"><a class="page-link" href="<?= Url::current(['sort' => $sortAttributeId]) ?>"><?= HtmlPurifier::process($sortAttributeValue) ?></a></li>
                    <?php endif ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="card-columns">
        <?php
        foreach (Blog::getData('products') as $product) :
            $title = HtmlPurifier::process($product['title']);
        ?>
            <a class="card" href="<?= Blog::url('site/product', ['id' => $product['id']]) ?>">
                <?php
                if ($product['image']) :
                    echo Html::img(Blog::getImage('product', '400', $product['image']), ['class' => 'img-fluid rounded', 'alt' => $title]);
                endif;
                ?>
                <div class="card-body p-3">
                    <h5 class="card-title"><?= $title ?></h5>
                    <p class="card-text">
                        <?php
                        foreach ($product['_fields'] as $field) :
                            if ($field['in_summary'] !== "0") :
                                echo ' <strong> ' . HtmlPurifier::process($field['field']) . ' : ' . '</strong> ';
                                echo HtmlPurifier::process(implode(' ,', $field['values']) . ' ' . $field['unit']);
                                echo '<br>';
                            endif;
                        endforeach;
                        ?>
                    </p>

                    <?php
                    if (empty($product['price_min']) && empty($product['price_max'])) :
                    else :
                        echo '<p class="text-left">';
                        if (!empty($product['price_min']) && !empty($product['price_max'])) :
                            if ($product['price_min'] == $product['price_max']) :
                                echo Yii::$app->formatter->asPrice($product['price_min']) . '</p>';
                            else :
                                echo ' از ' . Yii::$app->formatter->asPrice($product['price_min']) . '<br>' . ' تا ' . Yii::$app->formatter->asPrice($product['price_max']);
                            endif;
                        else :
                            if (!empty($product['price_min'])) :
                                echo ' از ' . Yii::$app->formatter->asPrice($product['price_min']);
                            endif;
                            if (!empty($product['price_max'])) :
                                echo ' تا ' . Yii::$app->formatter->asPrice($product['price_max']);
                            endif;
                        endif;
                        echo '</p>';
                    endif;

                    ?>
                </div>
            </a>
        <?php endforeach ?>
    </div>




    <div class="row">
        <div class="col-sm-12">
            <?= LinkPager::widget(['pagination' => $pagination, 'hideOnSinglePage' => false]); ?>
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