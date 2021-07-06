<?php

use app\models\Blog;
use yii\captcha\Captcha;
use yii\bootstrap4\ActiveForm;

if (!isset($colClass)) {
    $colClass = 'col-sm-6 offset-sm-3';
}

if (!isset($activeFormId)) {
    $activeFormId = null;
}

if (!isset($showHeader)) {
    $showHeader = true;
}

if (!isset($scenario)) {
    $scenario = $model->scenario;
}

if ($scenario == 'verifyRequest') {
    $this->title = Yii::t('app', 'Verify Request');
    $formAction = Blog::url('site/verify-request');
    $visibleInputs = ['mobile'];
} elseif ($scenario == 'verify') {
    $this->title = Yii::t('app', 'Verify');
    $formAction = Blog::url('site/verify');
    $visibleInputs = ['mobile', 'verify_token'];
} elseif ($scenario == 'resetPasswordRequest') {
    $this->title = Yii::t('app', 'Reset Password Request');
    $formAction = Blog::url('site/reset-password-request');
    $visibleInputs = ['mobile'];
} elseif ($scenario == 'resetPassword') {
    $this->title = Yii::t('app', 'Reset Password');
    $formAction = Blog::url('site/reset-password');
    $visibleInputs = ['mobile', 'password', 'reset_token'];
} else {
    $scenario = 'login';
    $this->title = Yii::t('app', 'Login');
    $formAction = Blog::url('site/login');
    $visibleInputs = ['mobile', 'password', 'captcha'];
}

$this->registerCss("
.invalid-feedback {
    display: block;
}
");

?>

<div class="row">
    <div class="<?= $colClass ?>">
        <?php if ($showHeader) { ?>
            <h3 class="text-center mb-3"><?= $this->title ?></h3>
        <?php } ?>
        <?php
        $form = ActiveForm::begin([
            'id' => $activeFormId,
            'action' => $formAction,
            'fieldConfig' => [
                'template' => '<div class="input-group"><div class="input-group-prepend">{label}</div>{input}</div>{error}{hint}',
                'labelOptions' => [
                    'class' => 'input-group-text',
                ],
            ]
        ]);
        if (in_array('mobile', $visibleInputs)) {
            echo $form->field($model, 'mobile')->textInput()->hint(Yii::t('app', 'for example: 09123456789'));
        }
        if (in_array('verify_token', $visibleInputs)) {
            echo $form->field($model, 'verify_token')->textInput();
        }
        if (in_array('reset_token', $visibleInputs)) {
            echo $form->field($model, 'reset_token')->textInput();
        }
        if (in_array('password', $visibleInputs)) {
            echo $form->field($model, 'password')->passwordInput();
        }
        if (in_array('captcha', $visibleInputs)) {
            echo $form->field($model, 'captcha', ['template' => '{input}{error}{hint}'])->widget(Captcha::class, [
                'template' => '<div class="input-group"><div class="input-group-prepend"><div class="input-group-text p-0">{image}</div></div>{input}</div>',
                'options' => ['class' => 'form-control form-control-lg',],
                'imageOptions' => ['style' => 'max-height: 46px;',]
            ])->hint(Yii::t('app', 'if the captcha is illegible, click on it.'));
        }
        ?>
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block"> <?= $this->title ?> </button>
        </div>
        <?php ActiveForm::end(); ?>
        <?php
        if (in_array($scenario, ['login', 'resetPassword'])) {
        ?>
            <div class="form-group">
                <a type="button" class="btn btn-secondary" href="<?= Blog::url('site/reset-password-request', [$model->formName() . '[mobile]' => $model->mobile]) ?>"><?= Yii::t('app', 'Reset Password Request') ?></a>
            </div>
        <?php
        }
        if (in_array($scenario, ['login', 'verify'])) {
        ?>
            <div class="form-group">
                <a type="button" class="btn btn-secondary" href="<?= Blog::url('site/verify-request', [$model->formName() . '[mobile]' => $model->mobile]) ?>"><?= Yii::t('app', 'Verify Request') ?></a>
            </div>
        <?php
        }
        ?>
    </div>
</div>