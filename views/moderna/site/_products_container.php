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
.fs09 {
    font-size: 0.9rem;
}
.fs10 {
    font-size: 1.0rem;
}
.row.equal {
    display: flex;
    flex-wrap: wrap;
}
.thumbnail {
    color: #000000;
}
a.thumbnail {
    text-decoration: none;
}
.thumbnail img {
    text-decoration: none;
}
.thumbnail .caption * {
    margin: 9px 0 0;
}
.thumbnail .caption h5 {
    font-weight: bold;
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

    <div class="container-fluid">
        <div class="row equal">
            <?php
            foreach (Blog::getData('products') as $product) :
                $title = HtmlPurifier::process($product['title']);
            ?>
                <a class="thumbnail border pt-3 pb-3 col-xs-12 col-sm-6 col-md-4 col-lg-3" href="<?= Blog::url('site/product', ['id' => $product['id'], 'product_title' => $product['title'], 'product_code' => $product['code']]) ?>">
                    <?php
                    if ($product['image']) :
                        echo Html::img(Blog::getImage('product', '400', $product['image']), ['class' => 'img-fluid rounded', 'alt' => $title]);
                    endif;
                    ?>
                    <div class="card-body p-2">
                        <h5 class="card-title fs10 font-weight-bold"><?= $title ?></h5>
                        <h6 class="card-title fs09"><strong><?= Yii::t('app', 'Code') ?>: </strong><?= HtmlPurifier::process($product['code'])  ?></h6>
                        <p class="card-text fs09">
                            <?php
                            foreach ($product['_fields'] as $field) :
                                if ($field['in_summary'] !== "0") :
                                    echo ' <strong> ' . HtmlPurifier::process($field['field']) . ': ' . '</strong> ';
                                    echo HtmlPurifier::process(implode(' ,', $field['values']) . ' ' . $field['unit']);
                                    echo '<br>';
                                endif;
                            endforeach;
                            ?>
                        </p>

                        <?php
                        if (empty($product['price_min']) && empty($product['price_max'])) :
                        else :
                            echo '<p class="text-left m-0">';
                            if (!empty($product['price_min']) && !empty($product['price_max'])) :
                                if ($product['price_min'] == $product['price_max']) :
                                    echo Yii::$app->formatter->asPrice($product['price_min']);
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