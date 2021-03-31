<?php

use app\models\Blog;
use yii\helpers\HtmlPurifier;
use yii\bootstrap4\Breadcrumbs;

$this->title = Blog::getData('category', 'title');

if (Yii::$app->view->params['category']['des']) {
    $this->registerMetaTag([
        'name' => 'description',
        'content' => Yii::$app->view->params['category']['des'],
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
        ['label' => Yii::$app->view->params['_categories'][Yii::$app->view->params['categoryId']]],
    ],
]);
?>

<div class="row">
    <div class="col-sm-12 pb20">
        <h1 class="mt0"><?= HtmlPurifier::process(Yii::$app->view->params['_categories'][Yii::$app->view->params['categoryId']]) ?></h1>
    </div>
</div>

<?php if (Yii::$app->view->params['category']['des']) : ?>
    <div class="row">
        <div class="col-sm-12">
            <p class="text-justify"><?= HtmlPurifier::process(Yii::$app->view->params['category']['des']) ?></p>
        </div>
    </div>
<?php endif; ?>

<?= $this->render('_products_container') ?>