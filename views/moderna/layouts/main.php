<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\cdn\FontawesomeAsset;
use app\components\Alert;
use yii\helpers\Html;
use yii\bootstrap4\Breadcrumbs;
use app\assets\ModernaAsset;
use app\assets\ModernaRtlAsset;
use app\models\Blog;
use yii\widgets\Spaceless;

if (Blog::isRtl()) {
    ModernaRtlAsset::register($this);
} else {
    ModernaAsset::register($this);
}
FontawesomeAsset::register($this);

$this->registerMetaTag([
    'name' => 'keywords',
    'content' => Blog::getMetaKeyword(),
]);
$blogSlug = Blog::print('slug');
$this->title = Blog::normalizeArrayUnorder([$this->title, Blog::print('title'), $blogSlug], false, ' | ');
$this->registerMetaTag([
    'name' => 'description',
    'content' => (Blog::print('des') ? Blog::print('des') : Blog::normalizeArray([Blog::print('title'), $blogSlug, Blog::print('name')], false, ' - ')),
]);
?>
<?php $this->beginPage() ?>
<?php if (YII_ENV != 'dev') Spaceless::begin(); ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>" dir="<?= Blog::isRtl() ? 'rtl' : 'ltr' ?>">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link href="<?= Blog::getImage('logo', '128_128_20_1', Blog::print('logo')) ?>" rel="icon">

    <?php foreach (["apple-touch-icon", "icon",] as $relsValue) : ?>
        <?php foreach (Yii::$app->params['manifestIconSizes'] as $widthsValue) : ?>
            <link sizes="<?= $widthsValue . 'x' . $widthsValue ?>" href="<?= Blog::getImage('logo', $widthsValue . "_" . $widthsValue . "__1", Blog::print('logo')) ?>" rel="<?= $relsValue ?>">
        <?php endforeach; ?>
    <?php endforeach; ?>

    <meta name="msapplication-TileImage" content="<?= Blog::getImage('logo', "144_144__1", Blog::print('logo')) ?>">
    <meta name="msapplication-TileColor" content="#ffffff">

    <meta name="theme-color" content="<?= Yii::$app->params['manifestThemeColor'] ?>">
    <meta name="msapplication-navbutton-color" content="<?= Yii::$app->params['manifestThemeColor'] ?>">
    <meta name="apple-mobile-web-app-status-bar-style" content="<?= Yii::$app->params['manifestThemeColor'] ?>">

    <link rel="manifest" href="<?= Blog::url('/manifest.json') ?>">

    <?php $this->head() ?>
</head>

<body class="d-flex flex-column">
    <?php $this->beginBody() ?>
    <div class="wrap">
        <?= $this->render('navbar'); ?>
        <div class="container mt-2">
            <?= Breadcrumbs::widget([
                'links' => isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : [],
            ]) ?>
            <?= Alert::widget() ?>
            <div class="row">
                <div class="col-sm-3"><?= $this->render('mainmenu'); ?></div>
                <div class="col-sm-9"><?= $content ?></div>
            </div>
        </div>
    </div>
    <?= $this->render('footer') ?>
    <?php $this->endBody() ?>
</body>

</html>
<?php if (YII_ENV != 'dev') Spaceless::end(); ?>
<?php $this->endPage() ?>