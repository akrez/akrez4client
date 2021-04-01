<?php

use kartik\slider\Slider;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\web\JsExpression;

$idPrefix = 'Search-' . '-' . $i;
echo Html::hiddenInput($namePrefix . '[operation]', $widget);
?>

<div class="col-sm-12 filter">
    <div class="panel panel-default panel-akrez">
        <div class="panel-heading <?= $disabled ? 'panel-collapsed' : '' ?>" data-idPrefix="<?= $idPrefix ?>">
            <?= Html::checkbox(null, !$disabled); ?>
            <?= HtmlPurifier::process($field['title']) ?>
            <?= $field['unit'] ? ' <small>(' . HtmlPurifier::process($field['unit']) . ')</small> ' : '' ?>
        </div>
        <div class="panel-body" style="<?= $disabled ? 'display: none;' : '' ?>">



            <?php
            echo "<div><span style='float: left;' id='$idPrefix-label-min'>" . number_format($filter['value_min']) . "</span><span style='float: right;' id='$idPrefix-label-max'>" . number_format($filter['value_max']) . "</span><div class='clearfix'></div></div>";
            echo Slider::widget([
                'options' => [
                    'id' => $idPrefix . '-slider',
                ],
                'name' => $namePrefix . "[input]",
                'value' => implode(',', [$filter['value_min'], $filter['value_max']]),
                'sliderColor' => Slider::TYPE_GREY,
                'pluginOptions' => [
                    'min' => $min,
                    'max' => $max,
                    'range' => true,
                    'tooltip' => 'hide',
                ],
                'pluginEvents' => [
                    'slide' => new JsExpression("function( event ) { " .
                        "$('#{$idPrefix}-label-min').text(event.value[0].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')); " .
                        "$('#{$idPrefix}-label-max').text(event.value[1].toString().replace(/\B(?=(\d{3})+(?!\d))/g, ',')); " .
                        "$('#{$idPrefix}-input-min').val(event.value[0]); " .
                        "$('#{$idPrefix}-input-max').val(event.value[1]); " .
                        "}"),
                ],
            ]);

            echo Html::hiddenInput($namePrefix . '[value_min]', $filter['value_min'], ['id' => $idPrefix . '-input-min'] + ($disabled ? ['disabled' => 'disabled'] : []));
            echo Html::hiddenInput($namePrefix . '[value_max]', $filter['value_max'], ['id' => $idPrefix . '-input-max'] + ($disabled ? ['disabled' => 'disabled'] : []));
            ?>



        </div>
    </div>
</div>