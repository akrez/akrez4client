<?php

use app\models\Customer;
use yii\captcha\Captcha;
use yii\bootstrap4\ActiveForm;

$this->registerCss("
.invalid-feedback {
    display: block;
}
");
?>

<?php
$form = ActiveForm::begin([
    'id' => 'login-form',
    'fieldConfig' => [
        'template' => '<div class="input-group"><div class="input-group-prepend">{label}</div>{input}</div>{error}{hint}',
        'labelOptions' => [
            'class' => 'input-group-text',
        ],
    ]
]);
?>
<?= $form->field($model, 'mobile')->textInput()->hint(Yii::t('app', 'for example: 09123456789')) ?>
<?= $form->field($model, 'password')->passwordInput() ?>
<?= $form->field($model, 'captcha', ['template' => '{input}{error}{hint}'])->widget(Captcha::class, [
    'template' => '<div class="input-group"><div class="input-group-prepend"><div class="input-group-text p-0">{image}</div></div>{input}</div>',
    'options' => ['class' => 'form-control form-control-lg',],
    'imageOptions' => ['style' => 'max-height: 46px;',]
])->hint(Yii::t('app', 'if the captcha is illegible, click on it.')) ?>
<div class="form-group">
    <button type="submit" class="btn btn-primary btn-block" style="float: right;"> <?= Yii::t('app', 'Signup') ?> </button>
</div>
<?php ActiveForm::end(); ?>