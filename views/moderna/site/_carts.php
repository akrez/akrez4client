<?php

use app\assets\cdn\CompressorJsAsset;
use app\models\Blog;
use yii\web\View;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\HtmlPurifier;
use yii\widgets\Pjax;

CompressorJsAsset::register($this);

$this->registerCss('
.splash-style {
    display: none;
    background-color: rgba(0, 0, 0, 0.67);
    inset: 0px;
    position: absolute;
    z-index: 9998;
    background-repeat: no-repeat;
    background-position: center;
}

.row.equal {
    display: flex;
    flex-wrap: wrap;
}
.thumbnail {
    color: #000000;
    display: flex; 
    flex-direction:column;
}
a.thumbnail {
    text-decoration: none;
}
.thumbnail img {
    text-decoration: none;
}
.thumbnail .caption * {
    margin: 9px 0 0;
}
.thumbnail .caption h5 {
    font-weight: bold;
}
');
$this->registerJs('
var constants = ' . json_encode([
    'messages' => [
        'compressor_error' => Yii::t('yii', 'The file "{file}" is not an image.', ['file' => Yii::t('app', 'payment_name_file')]),
    ],
    'urls' => [
        'payment_add' => Blog::url('payment-add', ['render_cart' => 'true',]),
    ]
]) . ';

$(document).on("change", "#invoice-payment_name-handler", function(e) {
    var img = e.target.files[0];
    new Compressor(img, {
        quality: 0.99,
        maxWidth: 999,
        maxHeight: 999,
        success(result) {
            var reader = new FileReader();
            reader.readAsDataURL(result);
            reader.onloadend = function() {
                $.ajax({
                    type: "POST",
                    url: constants.urls.payment_add,
                    data: {
                        payment_name_file: reader.result
                    },
                    complete: function(jqXHR, textStatus) {
                        $(".ajax-splash-show").css("display","none");
                        $.pjax.reload("#blog-pjax");
                    },
                    beforeSend: function(jqXHR, settings) {
                        $(".ajax-splash-show").css("display","inline-block");
                    },
                    success: function(data, textStatus, jqXHR) {
                    }
                });
            }
        },
        error(err) {
            alert(constants.messages.compressor_error);
        },
    });
});

$(document).on("pjax:beforeSend", function(xhr, options) {
    $(".ajax-splash-show").css("display","inline-block");
});
$(document).on("pjax:complete", function(xhr, textStatus, options) {
    $(".ajax-splash-show").css("display","none");
});
', View::POS_READY);
?>

<div style="position: relative;">

    <?php
    Pjax::begin([
        'id' => "blog-pjax",
        'timeout' => false,
        'enablePushState' => false,
    ]);
    ?>

    <div class="ajax-splash-show splash-style"></div>
    <div class="row">
        <div class="col-sm-12">
            <?php
            foreach ((array)Blog::getData('carts') as $cart) {
                $package = Blog::getData('packages', $cart['package_id']);
                $product = Blog::getData('products', $package['product_id']);
                //
                $errors = [];
                foreach ($cart['errors'] as $cartErrors) {
                    foreach ($cartErrors as $cartError) {
                        $errors[] = $cartError;
                    }
                }
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
                                <?= HtmlPurifier::process(Yii::$app->formatter->asPrice($package['price'])) ?><span class="font-weight-bold"> ✖ <?= $cart['cnt'] ?></span>
                            </h6>
                            <?php
                            echo $this->render('_cart_cnt', [
                                'package' => $package,
                                'cart' => $cart,
                                'add' => false,
                                'renderCart' => true,
                            ]);
                            ?>
                            <small><?= Yii::t('app', 'Max per cart') . ' : ' . $package['max_in_cart'] ?></small>
                        </div>
                        <a class="col-sm-1 p-2 border-right text-center text-danger" data-pjax="true" href="<?= Blog::url('site/cart-delete', ['id' => $package['id'],  'render_cart' => 'true',]) ?>" data-confirm="<?= Yii::t('yii', 'Are you sure you want to delete this item?') ?>">
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
            <?php
            }
            ?>
        </div>
    </div>

    <div class="alert alert-secondary mt-0 mb-1" role="alert">
        <div class="row equal">
            <div class="mb-1 col-sm-12 col-md-8">
                <ul class="mb-0 pr-3">
                    <?php foreach (Blog::getFinancialAccount() as $account) : ?>
                        <li>
                            <?= Blog::getConstant('financial_account_identity_type', $account['identity_type']) ?> <?= HtmlPurifier::process($account['name']) ?>
                            <br />
                            <span class="text-monospace"><?= HtmlPurifier::process($account['identity']) ?></span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <h6 class="font-weight-bolder mt-2 mb-2"><?= Yii::t('app', 'Total Price') . ': ' . Yii::$app->formatter->asPrice(Blog::getData('price')) ?></h6>
                <label class="btn btn-info mb-1" for="invoice-payment_name-handler"><?= Yii::t('app', 'upload payment image') ?></label>
                <input type="file" id="invoice-payment_name-handler" class="d-none" />
            </div>
            <?php
            foreach (Blog::getData('payments') as $payment) :
            ?>
                <div class="thumbnail mb-1 col-sm-6 col-md-4">
                    <a style="position: relative" data-pjax="0" target="_blank" href="<?= Blog::getImage('payment', '_', $payment['payment_name']) ?>">
                        <?php
                        echo Html::img(Blog::getImage('payment', '400', $payment['payment_name']), ['class' => 'img-fluid rounded',]);
                        ?>
                    </a>
                    <a style="position: absolute; right: 25px; top: 10px" class="btn btn-danger btn-sm" data-pjax="true" href="<?= Blog::url('payment-delete', ['payment_id' => $payment['id'], 'render_cart' => 'true',]) ?>" data-confirm="<?= Yii::t('yii', 'Are you sure you want to delete this item?') ?>">
                        <i class="fas fa-trash"></i>
                    </a>
                </div>
            <?php endforeach ?>
        </div>
    </div>

    <?php Pjax::end(); ?>

</div>

<?php
$form = ActiveForm::begin([
    'action' => Url::current(),
    'fieldConfig' => [
        'template' => '<div class="input-group"><div class="input-group-prepend">{label}</div>{input}</div>{error}{hint}',
        'labelOptions' => [
            'class' => 'input-group-text',
        ],
    ],
]);
?>

<div class="row mt-0">
    <div class="col-sm-12">
        <?php
        echo $this->render('_delivery_table', [
            'allModels' => Blog::getData('deliveries'),
            'isSelectMode' => true,
        ]);
        ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <?= $form->field($model, 'des')->textarea(); ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <?= $form->errorSummary($model); ?>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <div class="form-group">
            <button type="submit" class="btn btn-primary"> <?= Yii::t('app', 'Invoice') ?> </button>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>