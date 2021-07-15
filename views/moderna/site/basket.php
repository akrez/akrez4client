<?php

use app\models\Blog;
use yii\bootstrap4\Breadcrumbs;

$this->title = Yii::t('app', 'Basket');
?>

<?=
Breadcrumbs::widget([
    'homeLink' => [
        'label' => Yii::t('yii', 'Home'),
        'url' => Blog::firstPageUrl(),
    ],
    'links' => [
        ['label' => $this->title],
    ],
]);
?>

<div class="row">
    <div class="col-sm-12 pb-2">
        <h1><?= $this->title ?></h1>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <?php if (Blog::getData('baskets')) : ?>
            <?= $this->render('_basket_table', ['editable' => true]) ?>
        <?php else : ?>
            <div class="alert alert-warning" role="alert">
                <?= Yii::t('yii', 'No results found.'); ?>
            </div>
        <?php endif; ?>
    </div>
</div>