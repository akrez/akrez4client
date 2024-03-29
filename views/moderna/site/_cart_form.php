<?php

use app\models\Blog;
use app\models\Model;
use yii\bootstrap4\Html;
use yii\helpers\HtmlPurifier;

$this->registerCss("
");
?>
<div class="card mb-1 border-<?= $errors ? 'danger' : 'secondary' ?>">
    <div class="row p-0 no-gutters card-body">
        <div class="col-sm-1 p-2 my-auto">
            <?= $product['image'] ? Html::img(Blog::getImage('product', '400', $product['image']), ["class" => "card-img"]) : '' ?>
        </div>
        <div class="col-sm-3 p-2 my-auto">
            <h6><?= HtmlPurifier::process($product['title']) ?></h6>
        </div>
        <div class="col-sm-4 p-2 my-auto">
            <h6><?= Yii::t('app', 'Guaranty') ?> <?= HtmlPurifier::process($package['guaranty']) ?></h6>
            <?php if ($package['des']) : ?>
                <h6><?= HtmlPurifier::process($package['des']) ?></h6>
            <?php endif; ?>
            <?php if ($package['color_code']) : ?>
                <h6>
                    <span class="border border-dark rounded" style="background-color: <?= $package['color_code'] ?>">⠀⠀</span>
                    <?= Blog::colorLabel($package['color_code']) ?>
                </h6>
            <?php endif; ?>
        </div>
        <div class="col-sm-3 p-2 my-auto">
            <h6 class="text-center">
                <span class="font-weight-bold"><?= $cart['cnt'] ?> ✖ </span><?= HtmlPurifier::process(Yii::$app->formatter->asPrice($package['price'])) ?>
            </h6>
            <?php
            echo $this->render('_cart_cnt', [
                'package' => $package,
                'cart' => $cart,
                'add' => false,
            ]);
            ?>
            <small><?= Yii::t('app', 'Max per cart') . ' : ' . $package['max_in_cart'] ?></small>
        </div>
        <a class="col-sm-1 p-2 border-right text-center text-danger" href="<?= Blog::url('site/cart-delete', ['id' => $package['id']]) ?>" data-confirm="<?= Yii::t('yii', 'Are you sure you want to delete this item?') ?>">
            <i class="fas fa-trash fa-2x"></i>
        </a>
    </div>
    <?php if ($package['price'] != $cart['price_initial']) : ?>
        <div class="row p-2 no-gutters card-body text-primary border-top">
            <?= Yii::t('app', 'price has changed from {price_old} to {price_new}', [
                'price_old' => Yii::$app->formatter->asPrice($cart['price_initial']),
                'price_new' => Yii::$app->formatter->asPrice($package['price']),
            ]); ?>
        </div>
    <?php endif; ?>
    <?php if ($errors) : ?>
        <div class="row p-2 no-gutters card-footer text-danger">
            <?= HtmlPurifier::process(implode('<br />', $errors)); ?>
        </div>
    <?php endif; ?>
</div>