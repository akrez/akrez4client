<?php

use app\models\Blog;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

$blogSlug = (Blog::print('slug') ? Blog::print('slug') : '');
$this->registerMetaTag([
    'name' => 'keywords',
    'content' => Blog::normalizeArrayUnorder([Blog::print('title'), $blogSlug, Blog::print('name')], false, ',') . (Blog::getData('_categories') ? ',' . implode(',', (array)Blog::getData('_categories')) : ''),
]);
?>

<div class="v1-default-index">
    <h1 class="mt-0" style="display: inline-block;"><?= Blog::print('title') ?></h1><h2 class="mt-0 h2text <?= Blog::isRtl() ? 'mr-2' : 'ml-2' ?>" style="display: inline-block;"><?= Blog::print('slug') ?></h2><?= HtmlPurifier::process($page) ?>
</div>

<div class="row pt-2 pb-2">
    <div class="col-sm-4">
        <?= Html::beginForm(Blog::url('site/index'), 'GET', ['class' => 'form-inline']); ?>
        <div class="input-group flex-fill">
            <?= Html::textInput('Product[title][0][value]', Blog::getData('Product', 'title', 0, 'value'), ['class' => 'form-control']); ?>
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