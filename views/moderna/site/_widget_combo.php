<?php

use app\models\Blog;
use yii\helpers\Html;

if ($filter['widget'] == "COMBO NUMBER") {
    $operationList = [
        '>=' => Blog::getConstant('widget', '>='),
        '<=' => Blog::getConstant('widget', '<='),
        '=' => Blog::getConstant('widget', '='),
        '<>' => Blog::getConstant('widget', '<>'),
    ];
} else {
    $operationList = [
        'LIKE' => Blog::getConstant('widget', 'LIKE'),
        'NOT LIKE' => Blog::getConstant('widget', 'NOT LIKE'),
        '=' => Blog::getConstant('widget', '='),
        '<>' => Blog::getConstant('widget', '<>'),
    ];
}
?>
<div class="input-group flex-fill">
    <div class="input-group-prepend">
        <?= Html::tag('span', Html::encode($field['title']), ['class' => 'input-group-text']) ?>
    </div>
    <?= Html::dropDownList($namePrefix . '[operation]', $filter['operation'], $operationList, ['class' => 'form-control']); ?>
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