<?php

use app\models\Blog;
use yii\bootstrap4\Html;
use yii\helpers\HtmlPurifier;

$editable = (!isset($editable) ? false : $editable);

foreach ((array)Blog::getData('carts') as $cart) {
    $package = Blog::getData('packages', $cart['package_id']);
    $product = Blog::getData('products', $package['product_id']);
    //
    $cartErrorsFlat = [];
    foreach ($cart['errors'] as $cartErrors) {
        foreach ($cartErrors as $cartError) {
            $cartErrorsFlat[] = $cartError;
        }
    }
?>
    <div class="card mb-1 border-<?= $cartErrorsFlat ? 'danger' : 'secondary' ?>">
        <div class="row p-0 no-gutters card-body">
            <div class="col-sm-2">
                <?= $product['image'] ? Html::img(Blog::getImage('product', '400', $product['image']), ["class" => "card-img"]) : '' ?>
            </div>
            <div class="col-sm-6 p-2 my-auto">
                <h5><?= HtmlPurifier::process($product['title']) ?></h5>
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
                <h5 class="text-center">
                    <?= HtmlPurifier::process(Yii::$app->formatter->asPrice($package['price'])) ?>
                </h5>
                <?php
                if ($package['stock'] > 0) {
                    echo $this->render('_cart_cnt', [
                        'package' => $package,
                        'cart' => $cart,
                        'add' => false,
                    ]);
                }
                ?>
            </div>
            <div class="col-sm-1 p-2 text-center my-auto">
                <a class=" text-danger" href="<?= Blog::url('site/cart-delete', ['id' => $package['id']]) ?>" data-confirm="<?= Yii::t('yii', 'Are you sure you want to delete this item?') ?>">
                    <i class="far fa-times-circle fa-2x"></i>
                </a>
            </div>
        </div>
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
        <?php if ($cartErrorsFlat) : ?>
            <div class="row p-2 no-gutters card-footer text-danger">
                <?= HtmlPurifier::process(implode('<br />', $cartErrorsFlat)); ?>
            </div>
        <?php endif; ?>
    </div>
<?php
}
?>