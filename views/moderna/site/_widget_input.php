<?php

use app\models\Blog;
use yii\helpers\Html;

?>
<div class="input-group flex-fill">
    <div class="input-group-prepend">
        <?= Html::tag('span', Html::encode($field['title']) . ' ' . Blog::getConstant('widget', $filter['widget']), ['class' => 'input-group-text']) ?>
    </div>
    <?= Html::hiddenInput($namePrefix . '[operation]', $filter['widget']); ?>
    <?= Html::textInput($namePrefix . '[value]', $filter['value'], ['class' => 'form-control']); ?>
    <?php if (mb_strlen($field['unit']) > 0) : ?>
        <div class="input-group-append">
            <?= Html::tag('span', Html::encode($field['unit']), ['class' => 'input-group-text']) ?>
            <?php
            /*
            if ($filter['value'] !== null) :
                Html::tag('span', '<i class="fa fa-trash" aria-hidden="true"></i>', ['class' => 'btn btn-danger btn-delete']);
            endif 
            */
            ?>
        </div>
    <?php endif ?>
</div>