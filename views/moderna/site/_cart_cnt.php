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
$renderCart = (isset($renderCart) && $renderCart ? 'true' : '');
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
        'render_cart' => $renderCart,
    ]), 'get', ['data-pjax' => '1',]);
?>
    <div class="p-0">
        <div class="input-group input-group-sm">
            <input name="cnt" type="number" value="<?= $cart['cnt'] ?>" min="1" max="<?= $package['max_in_cart'] ?>" class="form-control text-center">
            <div class="input-group-append">
                <button class="btn btn-sm btn-social btn-block text-center <?= $add ? 'btn-success' : 'btn-primary' ?>">
                    <?= $add ? Yii::t('app', 'Add') : Yii::t('yii', 'Update') ?>
                </button>
            </div>
        </div>
    </div>
<?php
    echo Html::endForm();
}
?>