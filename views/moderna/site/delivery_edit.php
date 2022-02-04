<?php

use app\models\Blog;
use yii\bootstrap4\Breadcrumbs;

$this->title = Yii::t('app', 'Edit delivery');
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

<div class="row mt-3">
    <div class="col-sm-12">
        <?= $this->render('_delivery_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>