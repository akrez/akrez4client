<?php

use app\models\Blog;
use kartik\slider\Slider;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use yii\web\JsExpression;


$min = 0;       //default value
$max = 100;     //default value

$items = array_keys($items);
$items = array_map('floatval', $items);
$items = Blog::filterArray($items);
if (count($items) > 0) {
    $min = min($items);
    $max = max($items);
}

if ($max == $min) {
    $max = $min + 1;
}

if (!strlen($filter['value_min'])) {
    $filter['value_min'] = $min;
}
if (!strlen($filter['value_max'])) {
    $filter['value_max'] = $max;
}
//
$min = floatval($min);
$max = floatval($max);
$filter['value_min'] = floatval($filter['value_min']);
$filter['value_max'] = floatval($filter['value_max']);


?>

<div class="card card-akrez">
    <div class="card-header p-2">
        <?= Html::checkbox($namePrefix . '[operation]', !$disabled, ['value' => 'BETWEEN', 'class' => 'ml-1']); ?>
        <?= HtmlPurifier::process($field['title']) ?>
        <?= $field['unit'] ? ' <small>(' . HtmlPurifier::process($field['unit']) . ')</small> ' : '' ?>
    </div>
    <div class="card-body text-center p-2 collapse <?= $disabled ? '' : 'show' ?>">

        <div>
            <?php
            echo Html::tag('span', number_format($filter['value_min']), ['class' => 'float-left', 'id' => $idPrefix . '-label-min']);
            echo Html::tag('span', number_format($filter['value_max']), ['class' => 'float-right', 'id' => $idPrefix . '-label-max']);
            echo '<div class="clearfix"></div>';
            echo Html::hiddenInput($namePrefix . '[value_min]', $filter['value_min'], ['id' => $idPrefix . '-input-min']);
            echo Html::hiddenInput($namePrefix . '[value_max]', $filter['value_max'], ['id' => $idPrefix . '-input-max']);
            ?>
        </div>

        <?php
        if (isset(Yii::$app->params['bsVersion'])) {
            $bsVersion = Yii::$app->params['bsVersion'];
        } else {
            $bsVersion = null;
        }
        Yii::$app->params['bsVersion'] = '4.6';
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
        Yii::$app->params['bsVersion'] = $bsVersion;
        ?>


    </div>
</div>