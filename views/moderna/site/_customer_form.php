<?php

use yii\captcha\Captcha;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;

$show = [
    'mobile' => in_array($model->scenario, ['signup', 'verifyRequest', 'verify', 'signin', 'resetPasswordRequest', 'resetPassword']),
    'verify_token' => in_array($model->scenario, ['verify']),
    'reset_token' => in_array($model->scenario, ['resetPassword']),
    'password' => in_array($model->scenario, ['signup', 'signin', 'resetPassword']),
    'captcha' => in_array($model->scenario, ['signup', 'signin']),
];

$mobile = Html::encode(Yii::$app->request->get('mobile'));
$verifyToken = Html::encode(Yii::$app->request->get('verify_token'));
$resetToken = Html::encode(Yii::$app->request->get('reset_token'));

if ($model->scenario == 'verifyRequest') {
    $buttonContent = Yii::t('app', 'Verify Request');
} elseif ($model->scenario == 'resetRequest') {
    $buttonContent = Yii::t('app', 'Reset Request');
} elseif ($model->scenario == 'resetPasswordRequest') {
    $buttonContent = Yii::t('app', 'Reset Password Request');
} elseif ($model->scenario == 'resetPassword') {
    $buttonContent = Yii::t('app', 'Reset Password');
} else {
    $buttonContent = Yii::t('app', ucfirst($model->scenario));
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
if ($show['mobile']) {
    echo $form->field($model, 'mobile')->textInput($mobile ? ['readonly' => true] : [])->hint(Yii::t('app', 'for example: 09123456789'));
}
if ($show['verify_token']) {
    echo $form->field($model, 'verify_token')->textInput($verifyToken ? ['readonly' => true] : []);
}
if ($show['reset_token']) {
    echo $form->field($model, 'reset_token')->textInput($resetToken ? ['readonly' => true] : []);
}
if ($show['password']) {
    echo $form->field($model, 'password')->passwordInput();
}
if ($show['captcha']) {
    echo $form->field($model, 'captcha', ['template' => '{input}{error}{hint}'])->widget(Captcha::class, [
        'template' => '<div class="input-group"><div class="input-group-prepend"><div class="input-group-text p-0">{image}</div></div>{input}</div>',
        'options' => ['class' => 'form-control form-control-lg',],
        'imageOptions' => ['style' => 'max-height: 46px;',]
    ])->hint(Yii::t('app', 'if the captcha is illegible, click on it.'));
}
?>
<div class="form-group">
    <button type="submit" class="btn btn-primary btn-block"> <?= $buttonContent ?> </button>
</div>
<?php ActiveForm::end(); ?>