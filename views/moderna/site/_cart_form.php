<?php

use app\models\Blog;
use app\models\Model;
use yii\bootstrap4\Html;
use yii\helpers\HtmlPurifier;

$this->registerCss("
.sahel-fd {
    font-family: sahel;
}
.card-ribbon {
    position: relative;
}
.ribbon {
    width: 100px;
    height: 100px;
    overflow: hidden;
    position: absolute;
}

.ribbon span {
    position: absolute;
    display: block;
    width: 200px;
    padding: 5px 0;
    background-color: navy;
    color: #fff;
    text-transform: uppercase;
    text-align: center;
    right: -35px;
    top: 20px;
    transform: rotate(-45deg);
}
");
?>
<div class="card card-ribbon mb-1 border-<?= $errors ? 'danger' : 'secondary' ?>">
    <div class="row p-0 no-gutters card-body">
        <div class="col-sm-1 p-2 my-auto">
            <?= $product['image'] ? Html::img(Blog::getImage('product', '400', $product['image']), ["class" => "card-img"]) : '' ?>
        </div>
        <div class="col-sm-3 p-2 my-auto">
            <h6><?= HtmlPurifier::process($product['title']) ?></h6>
            <h6 class="text-center">
                <?= HtmlPurifier::process(Yii::$app->formatter->asPrice($package['price'])) ?>
            </h6>
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
            <?php if ($editable) { ?>
                <?php if ($package['stock'] > 0) {
                    echo $this->render('_cart_cnt', [
                        'package' => $package,
                        'cart' => $cart,
                        'add' => false,
                    ]);
                }
            } else { ?>
                <h6 class="text-center">
                    <i class="fas fa-times"> <span class="sahel-fd"> <?= $cart['cnt'] ?> </span> </i>
                </h6>
            <?php } ?>
        </div>
        <div class="col-sm-1 p-2 my-auto text-center">
            <?php if ($editable) { ?>
                <a class="text-danger" href="<?= Blog::url('site/cart-delete', ['id' => $package['id']]) ?>" data-confirm="<?= Yii::t('yii', 'Are you sure you want to delete this item?') ?>">
                    <i class="fas fa-trash fa-2x"></i>
                </a>
            <?php } ?>
        </div>
    </div>
    <?php if ($editable) : ?>
        <?php if ($package['price'] != $cart['price_initial']) : ?>
            <div class="row p-2 no-gutters card-body text-primary border-top">
                <?= Yii::t('app', 'price has changed from {price_old} to {price_new}', [
                    'price_old' => Yii::$app->formatter->asPrice($cart['price_initial']),
                    'price_new' => Yii::$app->formatter->asPrice($package['price']),
                ]); ?>
            </div>
        <?php endif; ?>
        <?php if ($package['stock'] < $cart['cnt']) : ?>
            <div class="row p-2 no-gutters card-footer text-primary">
                <?= Yii::t('app', 'Inventory left in stock is less than the specified amount'); ?>
            </div>
        <?php endif; ?>
        <?php if ($errors) : ?>
            <div class="row p-2 no-gutters card-footer text-danger">
                <?= HtmlPurifier::process(implode('<br />', $errors)); ?>
            </div>
        <?php endif; ?>
    <?php endif; ?>
</div>