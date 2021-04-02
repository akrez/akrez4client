<?php

use yii\helpers\Html;

$i = 0;
$fieldId = $field['title'];
$widgets = ($field['widgets'] ? (array) $field['widgets'] : []);

foreach ($widgets as $widget) :

    $idPrefix = 'Search-' . $section . '-' . $i;
    $namePrefix = $section . '[' . $fieldId . '][' . $i . ']';

    $filter = [
        'operation' => null,
        'value' => null,
        'values' => [],
        'value_min' => null,
        'value_max' => null,
        'widget' => $widget,
        'field' => $fieldId,
    ];

    $disabled = true;
    foreach ($searchParams as $fieldFilterKey => $fieldFilter) :
        if ($fieldFilter['widget'] == $widget) {
            $filter = $searchParams[$fieldFilterKey];
            unset($searchParams[$fieldFilterKey]);
            $disabled = false;
            break;
        }
    endforeach;

    echo '<div class="col-sm-12 pb-2">';
    echo Html::hiddenInput($namePrefix . '[widget]', $widget);
    if (in_array($widget, ["LIKE", "NOT LIKE", "=", "<>", ">=", "<=",])) :
        echo $this->render('/site/_widget_input', [
            'namePrefix' => $namePrefix,
            'field' => $field,
            'filter' => $filter,
        ]);
    elseif (in_array($widget, ["COMBO STRING", "COMBO NUMBER",])) :
        echo $this->render('/site/_widget_combo', [
            'namePrefix' => $namePrefix,
            'field' => $field,
            'filter' => $filter,
        ]);
    elseif (in_array($widget, ["SINGLE", "MULTI",])) :
        echo $this->render('/site/_widget_check', [
            'namePrefix' => $namePrefix,
            'field' => $field,
            'filter' => $filter,
            'disabled' => $disabled,
        ]);
    elseif (in_array($widget, ["BETWEEN",])) :
        echo $this->render('/site/_widget_between', [
            'namePrefix' => $namePrefix,
            'field' => $field,
            'filter' => $filter,
            'disabled' => $disabled,
            'idPrefix' => $idPrefix,
        ]);
    endif;
    echo '</div>';

    $i++;
endforeach;
