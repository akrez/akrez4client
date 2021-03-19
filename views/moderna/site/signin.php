<?php

use yii\bootstrap4\ActiveForm;
use app\models\Blog;
?>

<div class="row">
    <div class="col-sm-5">
        <h1 class="mb-3"><?= Yii::t('app', 'Signin') ?></h1>
        <?php
        $form = ActiveForm::begin([
            'id' => 'login-form',
            'fieldConfig' => [
                'template' => "{beginWrapper}\n<div class='input-group-prepend'>{label}</div>\n{input}\n{hint}\n{error}\n{endWrapper}",
                'wrapperOptions' => [
                    'class' => 'input-group'
                ],
                'labelOptions' => [
                    'class' => 'input-group-text'
                ],
            ],
        ]);
        ?>
        <?= $form->field($model, 'email')->textInput() ?>
        <?= $form->field($model, 'password')->passwordInput() ?>
        <div class="form-group">
            <button type="submit" class="btn btn-primary btn-block" style="float: right;"> <?= Yii::t('app', 'Signin') ?> </button>
        </div>
        <div class="form-group">
            <a type="button" class="btn btn-secondary" style="margin-top: 20px;float: right;" href="<?= Blog::url('site/reset-password-request') ?>"><?= Yii::t('app', 'Reset Password Request') ?></a>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>