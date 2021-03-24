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

<div class="input-group">
    <span class="input-group-addon">
        <?= Html::tag('label', $field['title'], ['class' => 'control-label', 'style' => 'margin-top: 3px;margin-bottom: 2px;']) ?>
    </span>
    <?= Html::dropDownList($namePrefix . '[operation]', $filter['operation'], [], ['class' => 'form-control', 'style' => 'width: 40%; padding: 4px;']); //$specialWidgets   
    ?>
    <?= Html::textInput($namePrefix . '[value]', $filter['value'], ['class' => 'form-control', 'style' => 'width: 60%; padding: 4px;']); ?>
    <?php if ($filter['value'] !== null) : ?>
        <span class="input-group-btn">
            <button class="btn btn-danger btn-delete" type="button" style="height: 34px;padding-top: 9px;">
                <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
            </button>
        </span>
    <?php endif ?>
</div>