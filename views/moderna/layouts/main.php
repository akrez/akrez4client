<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\assets\cdn\FontawesomeAsset;
use app\components\Alert;
use yii\helpers\Html;
use yii\bootstrap4\Breadcrumbs;
use app\assets\ModernaAsset;
use app\models\Blog;
use yii\widgets\Spaceless;

ModernaAsset::register($this);
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
<html lang="<?= Yii::$app->language ?>" dir="rtl">

<head>
    <meta charset="<?= Yii::$app->charset ?>">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php $this->registerCsrfMetaTags() ?>
    <title><?= Html::encode($this->title) ?></title>
    <link href="<?= Blog::getImage('logo', '128_128_20_1', Blog::print('logo')) ?>" rel="icon">
    <?php $this->head() ?>
</head>

<body>
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
    <?php $this->endBody() ?>
</body>

</html>
<?php if (YII_ENV != 'dev') Spaceless::end(); ?>
<?php $this->endPage() ?>