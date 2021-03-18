<?php

use app\components\BlogHelper;
use yii\bootstrap\Nav;
use yii\bootstrap\NavBar;
use yii\helpers\Html;

NavBar::begin([
    'brandLabel' => Html::tag('span', Yii::$app->blog->attribute('title'), ['style' => 'margin: 0px;font-size: 18px;']),
    'brandUrl' => BlogHelper::blogFirstPageUrl(),
    'renderInnerContainer' => false,
    'options' => [
        'class' => 'navbar navbar-default',
    ],
]);

if (isset(Yii::$app->view->params['_categories']) && Yii::$app->view->params['_categories']) {
    $menuItems = [];
    foreach (Yii::$app->view->params['_categories'] as $categoryId => $category) {
        $menuItems[] = ['label' => $category, 'url' => BlogHelper::url('site/category', ['id' => $categoryId])];
    }
    echo Nav::widget([
        'items' => [
            [
                'label' => Yii::t('app', 'Products Categories'),
                'items' => $menuItems,
            ],
        ],
        'options' => ['class' => 'navbar-nav navbar-right'],
    ]);
}

if (Yii::$app->user->isGuest) {
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-left'],
        'items' => [
            ['label' => Yii::t('app', 'Signup'), 'url' => BlogHelper::url('site/signup')],
            ['label' => Yii::t('app', 'Signin'), 'url' => BlogHelper::url('site/signin')],
        ],
    ]);
} else {
    echo Nav::widget([
        'options' => ['class' => 'navbar-nav navbar-left'],
        'items' => [
            [
                'label' => strtoupper(Yii::$app->user->getIdentity()->email),
                'items' => [
                    ['label' => Yii::t('app', 'Basket'), 'url' => BlogHelper::url('site/basket')],
                    ['label' => Yii::t('app', 'Invoice'), 'url' => BlogHelper::url('site/invoice')],
                    ['label' => Yii::t('app', 'Signout'), 'url' => BlogHelper::url('site/signout')],
                ],
            ],
        ],
    ]);
}
?>

<?= Html::beginForm(BlogHelper::blogFirstPageUrl(), 'GET', ['class' => 'navbar-form navbar-left']); ?>
<div class="input-group">
    <div class="form-group">
        <?= Html::textInput('Search[title][0][value]', null, ['class' => 'form-control']) ?>
        <?= Html::hiddenInput('Search[title][0][operation]', 'LIKE') ?>
    </div>
    <span class="input-group-btn">
        <?= Html::submitButton('<span class="glyphicon glyphicon-search" aria-hidden="true" style="font-size: 18px;"></span>', ['style' => 'height: 34px;', 'class' => 'btn btn-default']); ?>
    </span>
</div>
<?= Html::endForm(); ?>

<?php NavBar::end(); ?>