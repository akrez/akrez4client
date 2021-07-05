<?php

use yii\captcha\Captcha;
use yii\bootstrap4\ActiveForm;

if (!isset($scenario)) {
    $scenario = $model->scenario;
}

$visibleInputs = [
    'mobile' => in_array($scenario, ['login', 'verifyRequest', 'verify', 'resetPasswordRequest', 'resetPassword']),
    'verify_token' => in_array($scenario, ['verify']),
    'reset_token' => in_array($scenario, ['resetPassword']),
    'password' => in_array($scenario, ['login', 'resetPassword']),
    'captcha' => in_array($scenario, ['login']),
];

if ($scenario == 'verifyRequest') {
    $buttonTitle = Yii::t('app', 'Verify Request');
} elseif ($scenario == 'resetRequest') {
    $buttonTitle = Yii::t('app', 'Reset Request');
} elseif ($scenario == 'resetPasswordRequest') {
    $buttonTitle = Yii::t('app', 'Reset Password Request');
} elseif ($scenario == 'resetPassword') {
    $buttonTitle = Yii::t('app', 'Reset Password');
} else {
    $buttonTitle = Yii::t('app', ucfirst($scenario));
}

$this->registerCss("
.invalid-feedback {
    display: block;
}
");

$form = ActiveForm::begin([
    'id' => 'login-form',
    'fieldConfig' => [
        'template' => '<div class="input-group"><div class="input-group-prepend">{label}</div>{input}</div>{error}{hint}',
        'labelOptions' => [
            'class' => 'input-group-text',
        ],
    ]
]);
if ($visibleInputs['mobile']) {
    echo $form->field($model, 'mobile')->textInput()->hint(Yii::t('app', 'for example: 09123456789'));
}
if ($visibleInputs['verify_token']) {
    echo $form->field($model, 'verify_token')->textInput();
}
if ($visibleInputs['reset_token']) {
    echo $form->field($model, 'reset_token')->textInput();
}
if ($visibleInputs['password']) {
    echo $form->field($model, 'password')->passwordInput();
}
if ($visibleInputs['captcha']) {
    echo $form->field($model, 'captcha', ['template' => '{input}{error}{hint}'])->widget(Captcha::class, [
        'template' => '<div class="input-group"><div class="input-group-prepend"><div class="input-group-text p-0">{image}</div></div>{input}</div>',
        'options' => ['class' => 'form-control form-control-lg',],
        'imageOptions' => ['style' => 'max-height: 46px;',]
    ])->hint(Yii::t('app', 'if the captcha is illegible, click on it.'));
}
?>
<div class="form-group">
    <button type="submit" class="btn btn-primary btn-block"> <?= $buttonTitle ?> </button>
</div>
<?php ActiveForm::end(); ?>