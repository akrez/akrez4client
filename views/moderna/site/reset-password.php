<?php

use app\models\Blog;

$this->title = Yii::t('app', 'Reset Password');

?>

<div class="row">
    <div class="col-sm-4">
    </div>
    <div class="col-sm-4">
        <h3 style="margin-bottom: 20px;"><?= $this->title ?></h3>
        <?= $this->render('_customer_form', [
            'model' => $model,
        ]) ?>
        <div class="form-group">
            <a type="button" class="btn btn-secondary" href="<?= Blog::url('site/reset-password-request', [$model->formName() . '[mobile]' => $model->mobile]) ?>"><?= Yii::t('app', 'Reset Password Request') ?></a>
        </div>
    </div>
</div>