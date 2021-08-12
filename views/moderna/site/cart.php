<?php

use app\models\Blog;
use yii\bootstrap4\Breadcrumbs;

$this->title = Yii::t('app', 'Cart');
$hasCarts = boolval(Blog::getData('carts'));
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

<?php if ($hasCarts) : ?>
    <div class="row">
        <div class="col-sm-12">
            <?= $this->render('_cart_table', ['editable' => true]) ?>
        </div>
    </div>
    <div class="row mt-3">
        <div class="col-sm-12">
            <?= $this->render('_invoice', ['model' => $model]) ?>
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