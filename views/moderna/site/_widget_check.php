<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

if ($isSingle) {
    $operation = '=';
    $input = Html::radioList($namePrefix . '[value]', $filter['value'], array_combine($items, $items), ['separator' => "<br />", 'encode' => false]);
    $noneBtnType = 'radio';
} else {
    $operation = 'IN';
    $input = Html::checkboxList($namePrefix . '[values][]', $filter['values'], array_combine($items, $items), ['separator' => "<br />", 'encode' => false]);
    $noneBtnType = 'checkbox';
}

?>
<div class="card card-akrez">
    <div class="card-header p-2">
        <?= Html::checkbox($namePrefix . '[operation]', !$disabled, ['value' => $operation, 'class' => 'ml-1']); ?>
        <?= HtmlPurifier::process($field['title']) ?>
        <?= $field['unit'] ? ' <small>(' . HtmlPurifier::process($field['unit']) . ')</small> ' : '' ?>
    </div>
    <div class="card-body p-2 collapse <?= $disabled ? '' : 'show' ?>">
        <?= $input ?>
        <a onclick="$(this).closest('.card-body').find('input[type=<?= $noneBtnType ?>]').prop('checked', false)" href="javascript:void(0);" style="padding-right: 15px;"><small class="control-label">(هیچکدام)</small></a>
    </div>
</div>