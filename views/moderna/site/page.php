<?php

use app\models\Blog;
use yii\helpers\HtmlPurifier;

$this->title = Blog::getConstant('page_entity_blog', $id);

$blogSlug = (Blog::print('slug') ? Blog::print('slug') : '');
$this->registerMetaTag([
    'name' => 'keywords',
    'content' => Blog::normalizeArrayUnorder([Blog::print('title'), $blogSlug, Blog::print('name')], false, ',') . (Blog::getData('_categories') ? ',' . implode(',', (array)Blog::getData('_categories')) : ''),
]);
?>

<div class="row">
    <div class="col-sm-12 pb-2">
        <h1 class="mt-0"><?= $this->title ?></h1>
        <?= HtmlPurifier::process($page) ?>
    </div>
</div>