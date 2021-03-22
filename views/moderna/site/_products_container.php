<?php

use app\models\Blog;
use app\models\FieldList;
use yii\data\Pagination;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\helpers\Url;
use yii\widgets\LinkPager;

$this->registerCss("
    .row.equal {
        display: flex;
        flex-wrap: wrap;
    }

    .thumbnail {
        margin: 0px;
        border-radius: 0;
        padding: 8px;
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

$pagination = new Pagination([
    'pageSizeParam' => 'page_size',
    'pageSize' => Yii::$app->view->params['pagination']['page_size'],
    'page' => Yii::$app->view->params['pagination']['page'],
    'totalCount' => Yii::$app->view->params['pagination']['total_count'],
]);
?>

<?php if (count(Yii::$app->view->params['products']) > 0) : ?>

    <div class="row">
        <div class="col-sm-12">
            <ul class="pagination">
                <?php foreach (Yii::$app->view->params['sort']['attributes'] as $sortAttributeId => $sortAttributeValue) : ?>
                    <?php if (Yii::$app->view->params['sort']['attribute'] == $sortAttributeId) : ?>
                        <li class="active"><a href="#"><?= HtmlPurifier::process($sortAttributeValue) ?></a></li>
                    <?php else : ?>
                        <li class=""><a href="<?= Url::current(['sort' => $sortAttributeId]) ?>"><?= HtmlPurifier::process($sortAttributeValue) ?></a></li>
                    <?php endif ?>
                <?php endforeach; ?>
            </ul>
        </div>
    </div>

    <div class="container-fluid">
        <div class="row equal">
            <?php foreach (Yii::$app->view->params['products'] as $product) : ?>
                <a class="thumbnail col-xs-12 col-sm-6 col-md-4 col-lg-3" href="<?= Blog::url('site/product', ['id' => $product['id']]) ?>">

                    <?php
                    if ($product['image']) :
                        echo Html::img(Blog::getImage('product', '400', $product['image']), ['class' => 'img img-responsive', 'style' => 'margin-left: auto; margin-right: auto; padding: 9px 9px 0;', 'alt' => HtmlPurifier::process($product['title'])]);
                    endif;
                    ?>

                    <div class="caption">

                        <?php
                        echo '<h5>' . HtmlPurifier::process($product['title']) . '</h5>';

                        if (isset(Yii::$app->view->params['productsFields'][$product['id']])) :
                            foreach (Yii::$app->view->params['productsFields'][$product['id']] as $field) :
                                if ($field['in_summary']) :
                                    echo ' <strong> ' . HtmlPurifier::process($field['title']) . ' : ' . '</strong> ';
                                    if ($field['type'] == FieldList::TYPE_BOOLEAN) :
                                        foreach ($field['values'] as $value) :
                                            if ($value) :
                                                echo ($field['label_yes'] ? HtmlPurifier::process($field['label_yes']) : '<span class="glyphicon glyphicon-ok" aria-hidden="true"></span>');
                                            else :
                                                echo ($field['label_no'] ? HtmlPurifier::process($field['label_no']) : '<span class="glyphicon glyphicon-remove" aria-hidden="true"></span>');
                                            endif;
                                        endforeach;
                                    else :
                                        echo HtmlPurifier::process(implode(' ,', $field['values']) . ' ' . $field['unit']);
                                    endif;
                                    echo '<br>';
                                endif;
                            endforeach;
                        endif;

                        if (empty($product['price_min']) && empty($product['price_max'])) :
                        else :
                            echo '<p style="text-align: left;">';
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
    </div>

    <div class="row">
        <div class="col-sm-12">
            <?= LinkPager::widget(['pagination' => $pagination, 'hideOnSinglePage' => false]); ?>
        </div>
    </div>

<?php else : ?>

    <div class="row">
        <div class="col-xs-12">
            <div class="alert alert-warning" role="alert">
                <?= Yii::t('yii', 'No results found.'); ?>
            </div>
        </div>
    </div>

<?php endif; ?>