<?php

use app\models\Blog;
use yii\helpers\HtmlPurifier;
use yii\bootstrap4\Breadcrumbs;

$this->title = Blog::getData('category', 'title');

if (Blog::getData('category', 'des')) {
    $this->registerMetaTag([
        'name' => 'description',
        'content' => Blog::getData('category', 'des'),
    ]);
}
?>

<?=
Breadcrumbs::widget([
    'homeLink' => [
        'label' => Yii::t('yii', 'Home'),
        'url' => Blog::firstPageUrl(),
    ],
    'links' => [
        ['label' => Blog::getData('category', 'title')],
    ],
]);
?>

<div class="row">
    <div class="col-sm-12 pb-2">
        <h1 class="mt-0"><?= HtmlPurifier::process(Blog::getData('category', 'title')) ?></h1>
    </div>
</div>

<?php if (Blog::getData('category', 'des')) : ?>
    <div class="row">
        <div class="col-sm-12">
            <p class="text-justify"><?= HtmlPurifier::process(Blog::getData('category', 'des')) ?></p>
        </div>
    </div>
<?php endif; ?>

<?= $this->render('_products_container') ?>