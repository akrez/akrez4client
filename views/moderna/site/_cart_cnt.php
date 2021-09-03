<?php

use app\models\Blog;
use yii\bootstrap4\Html;
use yii\web\View;

$this->registerJs('
function changePackageCnt(sender, cnt) {
    var input = $(sender).closest("form").find("input[name=cnt]").first();
    var newValue = (parseInt(input.val()) || 0) + cnt;
    if (newValue < 1) {
        newValue = 1;
    } else if (input.attr("data-max") && input.attr("data-max") < newValue) {
        newValue = input.attr("data-max");
    }
    input.val(newValue);
}
', View::POS_HEAD);
//
if (!isset($productId)) {
    $productId = null;
}
//
if (!isset($add)) {
    $add = true;
}
$add = boolval($add);
//
if (!isset($cart)) {
    $cart = [];
}
$cart = $cart + [
    'cnt' => 1
];
?>
<?php
if ($package['max_in_cart'] > 0) {
    echo Html::beginForm(Blog::url('site/cart-add', [
        'add' => $add,
        'package_id' => $package['id'],
        'product_id' => $productId,
    ]), 'get');
?>
    <div class="p-0">
        <div class="input-group input-group-sm">
            <div class="input-group-prepend">
                <div class="btn btn-success" onclick="changePackageCnt(this, +1)">
                    <i class="fa fa-plus" aria-hidden="true"></i>
                </div>
            </div>
            <input name="cnt" type="text" value="<?= $cart['cnt'] ?>" data-max="<?= $package['max_in_cart'] ?>" class="form-control text-center">
            <div class="input-group-append">
                <div class="btn btn-danger" onclick="changePackageCnt(this, -1)">
                    <i class="fa fa-minus" aria-hidden="true"></i>
                </div>
            </div>
        </div>
    </div>
    <div class="p-0 pt-1">
        <button class="btn btn-sm btn-social btn-block text-center <?= $add ? 'btn-success' : 'btn-primary' ?>">
            <i class="fa <?= $add ? 'fa-cart-plus' : 'fa-shopping-cart' ?>" aria-hidden="true"></i>
            <?= $add ? Yii::t('app', 'Add to cart') : Yii::t('app', 'Update cart') ?>
        </button>
    </div>
<?php
    echo Html::endForm();
}
?>