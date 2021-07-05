<?php



$this->title = Yii::t('app', 'Reset Password Request');

?>

<div class="row">
    <div class="col-sm-4">
    </div>
    <div class="col-sm-4">
        <h3 style="margin-bottom: 20px;"><?= $this->title ?></h3>
        <?= $this->render('_customer_form', [
            'model' => $model,
        ]) ?>
    </div>
</div>