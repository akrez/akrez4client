<?php

use app\models\Blog;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

$blogSlug = (Blog::print('slug') ? Blog::print('slug') : '');
$this->registerMetaTag([
    'name' => 'keywords',
    'content' => Blog::normalizeArrayUnorder([Blog::print('title'), $blogSlug, Blog::print('name')], false, ',') . (isset(Yii::$app->view->params['_categories']) && Yii::$app->view->params['_categories'] ? ',' . implode(',', Yii::$app->view->params['_categories']) : ''),
]);
?>

<div class="v1-default-index">
    <h1 class="mt-0" style="display: inline-block;"><?= Blog::print('title') ?></h1>
    <h2 class="mt-0 h2text" style="display: inline-block;margin-right: 10px;"><?= Blog::print('slug') ?></h2>
    <h3 class="mt-0" style="text-align: justify;line-height: 1.62em;font-size: 14px;"><?= Blog::print('des') ?></h3>
</div>

<div class="row pt-2 pb-2">
    <div class="col-sm-4">
        <?= Html::beginForm(Blog::url('site/index'), 'GET', ['class' => 'form-inline']); ?>
        <div class="input-group flex-fill">
            <?= Html::textInput('Product[title][0][value]', (isset(Yii::$app->view->params['Product']['title'][0]['value']) ? Yii::$app->view->params['Product']['title'][0]['value'] : null), ['class' => 'form-control']); ?>
            <?= Html::hiddenInput('Product[title][0][operation]', 'LIKE'); ?>
            <div class="input-group-append">
                <?= Html::submitButton(Yii::t('app', 'Search'), ['class' => 'btn btn-secondary btn-block']); ?>
            </div>
        </div>
        <?= Html::endForm(); ?>
    </div>
</div>

<?php
echo $this->render('_products_container') 
?>