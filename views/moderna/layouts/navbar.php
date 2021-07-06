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
if (Blog::print('instagram')) {
    $items[] = [
        'label' => '<div class="btn btn-danger btn-social" style="background-color: #ac2bac;"> ' . '<i class="fab fa-instagram"></i> ' . Blog::print('instagram') . '</div>',
        'url' => "https://www.instagram.com/" . Blog::print('instagram'),
        'encode' => false,
    ];
}
$items[] = $form;
echo Nav::widget([
    'options' => ['class' => 'navbar-nav nav ' . (Blog::isRtl() ? 'mr-auto' : 'ml-auto')],
    'items' => $items,
]);

$items = [];
if (Yii::$app->user->isGuest) {
    $items[] = [
        'label' => '<div class="btn btn-info btn-social"> ' . Yii::t('app', 'Login') . '<span class="far fa-user fa-lg" aria-hidden="true"></span></div>',
        'encode' => false,
        'options' => [
            'onclick' => "$('#_login_form').modal('show');",
        ],
        'linkOptions' => [
            'class' => 'nav-item pl-0',
        ],
    ];
    Modal::begin([
        'title' => Yii::t('app', 'Login'),
        'id' => '_login_form',
        'closeButton' => ['style' => "line-height: 1.25em;"],
        'dialogOptions' => [
            'class' => 'modal-dialog modal-dialog-centered',
        ],
    ]);
    echo $this->render('/site/_customer_form', [
        'model' => new Customer(['scenario' => 'login']),
    ]);
    Modal::end();
} else {
    $items[] = [
        'label' => Customer::print('mobile'),
        'items' => [
            //['label' => Yii::t('app', 'Basket'), 'url' => Blog::url('site/basket')],
            //['label' => Yii::t('app', 'Invoice'), 'url' => Blog::url('site/invoice')],
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