<?php

use app\models\Blog;

$this->title = Yii::t('app', 'Signup');

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
            <a type="button" class="btn btn-secondary mt-2" href="<?= Blog::url('site/verify') ?>"><?= Yii::t('app', 'Verify') ?></a>
        </div>
    </div>
</div>