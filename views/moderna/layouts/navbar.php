<?php

use app\models\Blog;
use app\models\Customer;
use yii\bootstrap4\Modal;
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
foreach (Blog::hasPage() as $pageKey => $pageStatus) {
    if ($pageStatus && $pageKey != 'Index') {
        $items[] = ['label' => Blog::getConstant('entity_page', 'Blog', $pageKey), 'url' => Blog::url('site/page', ['id' => $pageKey])];
    }
}
if ($items) {
    echo Nav::widget([
        'options' => ['class' => 'nav navbar-nav ',],
        'items' => $items,
    ]);
}
?>

<?php
$form = Html::beginForm(Blog::firstPageUrl(), 'GET', ['class' => 'form-inline nav-link ']) .
    '<div class="input-group flex-fill">' .
    Html::textInput('Product[title][0][value]', null, ['class' => 'form-control']) .
    Html::hiddenInput('Product[title][0][operation]', 'LIKE') .
    '<div class="input-group-append">' .
    Html::submitButton('<i class="fa fa-search" aria-hidden="true"></i>', ['class' => 'btn btn-info']) .
    '</div>' .
    '</div>' .
    Html::endForm();
?>

<?php
$items = [];
if (Blog::print('telegram')) {
    $items[] = Html::a('', Blog::getShareLink('telegram', Blog::print('telegram')), [
        'class' => 'fab fa-2x fa-telegram nav-link ' . (Blog::isRtl() ? 'pl-2' : 'pr-2'),
        'style' => 'color: #2c9ed4;',
    ]);
}
if (Blog::print('instagram')) {
    $items[] = Html::a('', Blog::getShareLink('instagram', Blog::print('instagram')), [
        'class' => 'fab fa-2x fa-instagram nav-link ' . (Blog::isRtl() ? 'pl-2' : 'pr-2'),
        'style' => 'color: #b900b8;',
    ]);
}
if (Blog::print('whatsapp')) {
    $items[] = Html::a('', Blog::getShareLink('whatsapp', Blog::print('whatsapp')), [
        'class' => 'fab fa-2x fa-whatsapp nav-link ' . (Blog::isRtl() ? 'pl-2' : 'pr-2'),
        'style' => 'color: #39d855;',
    ]);
}
if (Blog::print('telegram_user')) {
    $items[] = Html::a('', Blog::getShareLink('telegram_user', Blog::print('telegram_user')), [
        'class' => 'fab fa-2x fa-telegram nav-link ' . (Blog::isRtl() ? 'pl-2' : 'pr-2'),
        'style' => 'color: #4c75a3;',
    ]);
}
echo Nav::widget([
    'options' => ['class' => 'navbar-nav nav ' . (Blog::isRtl() ? 'mr-auto' : 'ml-auto')],
    'items' => ['<li class="nav-item">' . implode('', $items) . '</li>'],
]);

echo Nav::widget([
    'options' => ['class' => 'navbar-nav nav'],
    'items' => [$form],
]);

$items = [];
if (Yii::$app->user->isGuest) {
    $modalId = '_login_form';
    $items[] = '<li class="nav-item pl-0"><button type="button" class="btn btn-info btn-social" data-toggle="modal" data-target="#' . $modalId . '"><span class="far fa-user fa-lg" aria-hidden="true"></span>' . Yii::t('app', 'Login') . '</button></li>';
    Modal::begin([
        'title' => Yii::t('app', 'Login'),
        'id' => $modalId,
        'closeButton' => ['style' => "line-height: 1.25em;"],
        'dialogOptions' => [
            'class' => 'modal-dialog modal-dialog-centered',
        ],
    ]);
    echo $this->render('/site/customer', [
        'model' => new Customer(['scenario' => 'login']),
        'showHeader' => false,
        'colClass' => 'col-sm-12',
    ]);
    Modal::end();
} else {
    $items[] = [
        'label' => Customer::print('mobile'),
        'items' => [
            ['label' => Yii::t('app', 'Cart'), 'url' => Blog::url('site/cart')],
            ['label' => Yii::t('app', 'Orders'), 'url' => Blog::url('site/orders')],
            ['label' => Yii::t('app', 'Signout'), 'url' => Blog::url('site/signout')],
        ],
    ];
}
echo Nav::widget([
    'options' => ['class' => 'navbar-nav nav'],
    'items' => $items,
]);

?>

<?php NavBar::end(); ?>