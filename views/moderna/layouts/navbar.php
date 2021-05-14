<?php

use app\models\Blog;
use yii\bootstrap4\Nav;
use yii\bootstrap4\NavBar;
use yii\helpers\Html;

NavBar::begin([
    'brandLabel' => Html::tag('span', Blog::print('title'), ['style' => 'margin: 0px;font-size: 18px;']),
    'brandUrl' => Blog::firstPageUrl(),
    'options' => [
        'class' => 'navbar navbar-expand-lg navbar-light bg-light',
    ],
]);

$items = [];
if (Blog::categories()) {
    $menuItems = [];
    foreach (Blog::categories() as $categoryId => $category) {
        $menuItems[] = ['label' => $category, 'url' => Blog::url('site/category', ['id' => $categoryId])];
    }
    $items[] = [
        'label' => Yii::t('app', 'Categories'),
        'items' => $menuItems,
    ];
}
foreach (Blog::pages() as $pageKey) {
    if ($pageKey == 'Index') {
        continue;
    }
    $items[] = ['label' => Blog::getConstant('page_entity_blog', $pageKey), 'url' => Blog::url('site/page', ['id' => $pageKey])];
}
if ($items) {
    echo Nav::widget([
        'options' => ['class' => 'nav navbar-nav ' . (Blog::isRtl() ? '' : 'ml-auto'),],
        'items' => $items,
    ]);
}
?>

<?= Html::beginForm(Blog::firstPageUrl(), 'GET', ['class' => 'form-inline ' . (Blog::isRtl() ? 'mr-auto' : 'ml-auto')]); ?>
<div class="input-group flex-fill">
    <?= Html::textInput('Product[title][0][value]', null, ['class' => 'form-control']) ?>
    <?= Html::hiddenInput('Product[title][0][operation]', 'LIKE') ?>
    <div class="input-group-append">
        <?= Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i>', ['class' => 'btn btn-info']); ?>
    </div>
</div>
<?= Html::endForm(); ?>

<?php
/*
$items = [];
if (Yii::$app->user->isGuest) {
    $items[] = ['label' => Yii::t('app', 'Signup'), 'url' => Blog::url('site/signup')];
    $items[] = ['label' => Yii::t('app', 'Signin'), 'url' => Blog::url('site/signin')];
} else {
    $items[] = [
        'label' => strtoupper(Blog::print('email')),
        'items' => [
            ['label' => Yii::t('app', 'Basket'), 'url' => Blog::url('site/basket')],
            ['label' => Yii::t('app', 'Invoice'), 'url' => Blog::url('site/invoice')],
            ['label' => Yii::t('app', 'Signout'), 'url' => Blog::url('site/signout')],
        ],
    ];
}
echo Nav::widget([
    'options' => ['class' => 'navbar-nav nav'],
    'items' => $items,
]);
*/
?>

<?php NavBar::end(); ?>