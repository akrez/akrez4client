<?php

use app\models\Blog;
use yii\helpers\Html;

?>
<div class="input-group">
    <span class="input-group-addon">
        <?= Html::tag('label', $field['title'] . ' ' . Blog::getConstant('widget', $field['widget']), ['class' => 'control-label', 'style' => 'margin-top: 3px;margin-bottom: 2px;']) ?>
    </span>
    <?= Html::hiddenInput($namePrefix . '[operation]', $widget); ?>
    <?= Html::textInput($namePrefix . '[value]', $filter['value'], ['class' => 'form-control',]); ?>
    <?= (empty($field['unit']) ? '' : Html::tag('span', $field['unit'], ['class' => 'input-group-addon'])); ?>
    <?php if ($filter['value'] !== null) : ?>
        <span class="input-group-btn">
            <button class="btn btn-danger btn-delete" type="button" style="height: 34px;padding-top: 9px;">
                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </button>
        </span>
    <?php endif ?>
</div>