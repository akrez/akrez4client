<?php

use yii\helpers\Html;

$this->title = $message;
?>
<div class="jumbotron">
    <div style="font-size: 63px;"><?= Html::encode($exception->statusCode) ?></div>
    <h1 class="mt-0" style="font-size: 21px;"><?= nl2br(Html::encode($message)) ?></h1>
</div>
