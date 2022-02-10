<?php

use app\assets\cdn\CompressorJsAsset;
use app\models\Blog;
use yii\web\View;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\HtmlPurifier;

CompressorJsAsset::register($this);

$this->registerJs('
var messages = ' . json_encode([
    'compressor_error' => Yii::t('yii', 'The file "{file}" is not an image.', ['file' => $model->getAttributeLabel('receipt_file')])
]) . '
$("#invoice-receipt-handler").change(function(e) {
    var img = e.target.files[0];
    new Compressor(img, {
        quality: 0.99,
        maxWidth: 999,
        maxHeight: 999,
        success(result) {
            var reader = new FileReader();
            reader.readAsDataURL(result);
            reader.onloadend = function() {
                var base64data = reader.result;
                $("#invoice-receipt_file").val(base64data);
                $("#invoice-receipt-image").attr("src", base64data);
            }
        },
        error(err) {
            alert(messages.compressor_error);
        },

    });
});
', View::POS_READY);
?>
<div class="row">
    <div class="col-sm-12">
        <?php
        $form = ActiveForm::begin([
            'action' => Url::current(),
            'fieldConfig' => [
                'template' => '<div class="input-group"><div class="input-group-prepend">{label}</div>{input}</div>{error}{hint}',
                'labelOptions' => [
                    'class' => 'input-group-text',
                ],
            ]
        ]);

        echo $this->render('_delivery_table', [
            'allModels' => Blog::getData('deliveries'),
            'isSelectMode' => true,
        ]);

        ?>

        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-secondary" role="alert">
                    <div class="row">
                        <div class="col-sm-8">
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
                            <label class="btn btn-info mt-3 mb-3" for="invoice-receipt-handler"><?= Yii::t('app', 'upload payment receipt image') ?></label>
                            <?php
                            echo $form->field($model, 'receipt_file', ['options' => ['class' => 'd-none']])->hiddenInput();
                            ?>
                        </div>
                        <div class="col-sm-4">
                            <?= Html::tag('img', '', [
                                'id' => 'invoice-receipt-image',
                                'class' => 'img img-fluid',
                                'src' => ($model->receipt_file ? $model->receipt_file : false),
                            ]); ?>
                        </div>
                    </div>
                </div>
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
        <input type="file" id="invoice-receipt-handler" class="d-none" />
    </div>
</div>