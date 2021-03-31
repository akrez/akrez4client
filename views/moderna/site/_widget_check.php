<?php

use yii\helpers\Html;
use yii\helpers\HtmlPurifier;

if ($isSingle) {
    $operation = '=';
    $input = Html::radioList($namePrefix . '[value]', $filter['value'], array_combine($items, $items), ['separator' => "<br />", 'encode' => false]);
} else {
    $operation = 'IN';
    $input = Html::checkboxList($namePrefix . '[values][]', $filter['values'], array_combine($items, $items), ['separator' => "<br />", 'encode' => false]);
}

?>
<div class="card mb-2 card-akrez">
    <div class="card-header">
        <?= Html::checkbox($namePrefix . '[operation]', !$disabled, ['value' => $operation]); ?>
        <?= HtmlPurifier::process($field['title']) ?>
        <?= $field['unit'] ? ' <small>(' . HtmlPurifier::process($field['unit']) . ')</small> ' : '' ?>
    </div>
    <div class="card-body p-2 collapse <?= $disabled ? '' : 'show' ?>">
        <?= $input ?>
        <a onclick="$(this).closest('.filter').find('input[type=radio]').prop('checked', false)" href="javascript:void(0);" style="padding-right: 15px;"><small class="control-label">(هیچکدام)</small></a>
    </div>
</div>