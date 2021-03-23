<?php

use app\models\Blog;
use app\models\FieldList;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
use kartik\slider\Slider;
use yii\web\JsExpression;
use yii\web\View;

$this->registerJs('
    $("body").on("click", ".btn-delete", function () {
        $(this).closest(".filter").find("input[type=text]").val("");
    });
    $(document).on("click", ".panel-akrez > .panel-heading", function (e) {
        var that = $(this);
        var idPrefix = that.attr("data-idprefix");
        var checkbox = that.find("input[type=checkbox]").first();
        if (checkbox.length < 1) {
            checkbox = null;
        }
        //
        showPanel = false;
        if (that.hasClass("panel-collapsed")) {
            showPanel = true;
        }
        //
        if (showPanel) {
            that.parents(".panel").find(".panel-body").slideDown();
            that.removeClass("panel-collapsed");
        } else {
            that.parents(".panel").find(".panel-body").slideUp();
            that.addClass("panel-collapsed");
        }
        if (idPrefix) {
            $("#" + idPrefix + "-input-min").prop("disabled", !showPanel);
            $("#" + idPrefix + "-input-max").prop("disabled", !showPanel);
        }
        if (checkbox) {
            checkbox.prop("checked", showPanel);
        }
    });

', View::POS_READY);
$this->registerCss('
    .slider {
	width: calc(100%) !important;
    }
    .panel-akrez .panel-heading {
        padding: 6px 12px;
        font-weight: 700;
        color: #555555;
        background-color: #eeeeee;
        cursor: pointer;
        user-select: none;
        margin-bottom: 0;
        display: block;
    }
    .panel-akrez .panel-body {
        padding: 6px 12px;
        max-height: 170px;
        overflow-y: auto;
    }
    .panel-akrez .panel-heading > input[type=checkbox] {
        margin-left: 12px;
        display: inline-block;
    }
');
?>

<?php if ($this->context->id == 'site' && in_array($this->context->action->id, ['category']) == false) : ?>
    <div class="row pb20">
        <div class="col-sm-12">
            <?php
            $blogLogo = Blog::getImage('logo', '400__67', Blog::print('logo'));
            $logo = Html::img($blogLogo, ['style' => 'margin: auto;', 'class' => 'img-fluid rounded', 'alt' => Blog::print('title')]);
            echo Html::a($logo, Blog::firstPageUrl(), ['style' => 'text-align: center;']);
            ?>
        </div>
    </div>
<?php endif; ?>

<?php if ($this->context->id == 'site' && in_array($this->context->action->id, ['category'])) : ?>

    <div class="row pb20">
        <div class="col-sm-12">
            <h4>جستجو</h4>
        </div>
    </div>

    <?= Html::beginForm(Blog::url('site/category', ['id' => Yii::$app->view->params['categoryId']]), 'GET'); ?>



    <div class="row">

        <?php
        $i = 0;
        foreach (Yii::$app->view->params['search'] as $fieldId => $fieldFilters) :
            $field = Yii::$app->view->params['fields'][$fieldId];
            $type = $field['type'];
            $widgets = (array) $field['widgets'];
            foreach ($widgets as $widget) :

                $filter = [
                    'operation' => null,
                    'value' => null,
                    'values' => [],
                    'value_min' => null,
                    'value_max' => null,
                ];

                foreach ((array) $fieldFilters as $fieldFilterKey => $fieldFilter) :
                    if ($fieldFilter['widget'] == $widget) {
                        $filter = Yii::$app->view->params['search'][$fieldId][$fieldFilterKey];
                        unset(Yii::$app->view->params['search'][$fieldId][$fieldFilterKey]);
                        continue;
                    }
                endforeach;

                $namePrefix = 'Search[' . $fieldId . '][' . $i . ']';
                echo Html::hiddenInput($namePrefix . '[widget]', $widget);

                if ($type == FieldList::TYPE_STRING || $type == FieldList::TYPE_NUMBER) :
                    if ($type == FieldList::TYPE_NUMBER) {
                        $specialWidgets = ['>=' => Blog::getConstant('widget', $type, '>='), '<=' => Blog::getConstant('widget', $type, '<='), '=' => Blog::getConstant('widget', $type, '='), '<>' => Blog::getConstant('widget', $type, '<>')];
                    } else {
                        $specialWidgets = ['LIKE' => Blog::getConstant('widget', $type, 'LIKE'), 'NOT LIKE' => Blog::getConstant('widget', $type, 'NOT LIKE'), '=' => Blog::getConstant('widget', $type, '='), '<>' => Blog::getConstant('widget', $type, '<>')];
                    }

                    if (in_array($widget, array_keys($specialWidgets))) :
        ?>
                        <div class="col-sm-12 pb20 filter">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <?= Html::tag('label', $field['title'] . ' ' . Blog::getConstant('widget', $type, $widget), ['class' => 'control-label', 'style' => 'margin-top: 3px;margin-bottom: 2px;']) ?>
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
                        </div>
                    <?php
                    elseif ($widget == 'COMBO') :
                    ?>
                        <div class="col-sm-12 pb20 filter">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <?= Html::tag('label', $field['title'], ['class' => 'control-label', 'style' => 'margin-top: 3px;margin-bottom: 2px;']) ?>
                                </span>
                                <?= Html::dropDownList($namePrefix . '[operation]', $filter['operation'], $specialWidgets, ['class' => 'form-control', 'style' => 'width: 40%; padding: 4px;']); ?>
                                <?= Html::textInput($namePrefix . '[value]', $filter['value'], ['class' => 'form-control', 'style' => 'width: 60%; padding: 4px;']); ?>
                                <?php if ($filter['value'] !== null) : ?>
                                    <span class="input-group-btn">
                                        <button class="btn btn-danger btn-delete" type="button" style="height: 34px;padding-top: 9px;">
                                            <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                        </button>
                                    </span>
                                <?php endif ?>
                            </div>
                        </div>
                    <?php
                    elseif ($widget == 'SINGLE') :
                        $items = Blog::normalizeArray($field['options'], true);
                        $disabled = !boolval($filter['value']);
                    ?>
                        <div class="col-sm-12 filter">
                            <div class="panel panel-default panel-akrez">
                                <div class="panel-heading <?= $disabled ? 'panel-collapsed' : '' ?>">
                                    <?= Html::checkbox(null, !$disabled); ?>
                                    <?= HtmlPurifier::process($field['title']) ?>
                                    <?= $field['unit'] ? ' <small>(' . HtmlPurifier::process($field['unit']) . ')</small> ' : '' ?>
                                </div>
                                <div class="panel-body" style="<?= $disabled ? 'display: none;' : '' ?>">
                                    <?= Html::hiddenInput($namePrefix . '[operation]', '='); ?>
                                    <?= Html::radioList($namePrefix . '[value]', $filter['value'], array_combine($items, $items), ['separator' => "<br />", 'encode' => false]) ?>
                                    <a onclick="$(this).closest('.filter').find('input[type=radio]').prop('checked', false)" href="javascript:void(0);" style="padding-right: 15px;"><small class="control-label">(هیچکدام)</small></a>
                                </div>
                            </div>
                        </div>

                    <?php
                    elseif ($widget == 'MULTI') :
                        $items = Blog::normalizeArray($field['options'], true);
                        $disabled = !boolval($filter['values']);
                    ?>

                        <div class="col-sm-12 filter">
                            <div class="panel panel-default panel-akrez">
                                <div class="panel-heading <?= $disabled ? 'panel-collapsed' : '' ?>">
                                    <?= Html::checkbox(null, !$disabled); ?>
                                    <?= HtmlPurifier::process($field['title']) ?>
                                    <?= $field['unit'] ? ' <small>(' . HtmlPurifier::process($field['unit']) . ')</small> ' : '' ?>
                                </div>
                                <div class="panel-body" style="<?= $disabled ? 'display: none;' : '' ?>">
                                    <?= Html::hiddenInput($namePrefix . '[operation]', 'IN'); ?>
                                    <?= Html::checkboxList($namePrefix . '[values][]', $filter['values'], array_combine($items, $items), ['separator' => "<br />", 'encode' => false]) ?>
                                    <a onclick="$(this).closest('.filter').find('input[type=checkbox]').prop('checked', false)" href="javascript:void(0);" style="padding-right: 15px;"><small class="control-label">(هیچکدام)</small></a>
                                </div>
                            </div>
                        </div>

                    <?php
                    elseif ($widget == 'BETWEEN') :
                        $idPrefix = 'Search-' . $fieldId . '-' . $i;
                        //
                        $min = 0;
                        $max = 100;
                        if ($fieldId == 'price') {
                            if (Yii::$app->view->params['category']['price_min']) {
                                $min = floatval(Yii::$app->view->params['category']['price_min']);
                            }
                            if (Yii::$app->view->params['category']['price_max']) {
                                $max = floatval(Yii::$app->view->params['category']['price_max']);
                            }
                        } elseif (isset($field['options'])) {
                            $items = Blog::normalizeArray($field['options'], true);
                            if (count($items) > 0) {
                                $min = reset($items);
                                $max = end($items);
                            }
                        }
                        $min = floatval($min);
                        $max = floatval($max == $min ? $max + 1 : $max);
                        //
                        $filter = $filter + ['value_min' => null, 'value_max' => null,];
                        //
                        $disabled = true;
                        if ($filter['value_min'] && $filter['value_max']) {
                            $disabled = false;
                        } elseif ($filter['value_min']) {
                            $filter['value_max'] = $max;
                            $disabled = false;
                        } elseif ($filter['value_max']) {
                            $filter['value_min'] = $min;
                            $disabled = false;
                        } else {
                            $filter['value_min'] = $min;
                            $filter['value_max'] = $max;
                        }
                        //
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

                    <?php
                    endif;
                elseif ($type == FieldList::TYPE_BOOLEAN) :
                    if ($widget == '2STATE') :
                        $idPrefix = 'Search-' . $fieldId . '-' . $i;
                    ?>
                        <div class="col-sm-12 filter">
                            <div class="panel panel-default panel-akrez">
                                <div class="panel-heading <?= $filter['value'] ? '' : 'panel-collapsed' ?>" for="<?= $idPrefix ?>">
                                    <?= Html::checkbox($namePrefix . '[value]', $filter['value'], ['id' => $idPrefix,]); ?>
                                    <?= Html::hiddenInput($namePrefix . '[operation]', '='); ?>
                                    <?= HtmlPurifier::process($field['title']) ?>
                                    <?= $field['unit'] ? ' <small>(' . HtmlPurifier::process($field['unit']) . ')</small> ' : '' ?>
                                </div>
                            </div>
                        </div>
                    <?php elseif ($widget == '3STATE') : ?>
                        <?php
                        $items = [
                            0 => (empty($field['label_no']) ? Yii::$app->formatter->booleanFormat[0] : $field['label_no']),
                            1 => (empty($field['label_yes']) ? Yii::$app->formatter->booleanFormat[1] : $field['label_yes']),
                        ];
                        ?>
                        <div class="col-sm-12 pb20 filter">
                            <div class="input-group">
                                <span class="input-group-addon">
                                    <?= Html::tag('label', $field['title'], ['class' => 'control-label', 'style' => 'margin-top: 3px;margin-bottom: 2px;']) ?>
                                </span>
                                <?= Html::hiddenInput($namePrefix . '[operation]', '='); ?>
                                <?= Html::dropDownList($namePrefix . '[value]', $filter['value'], $items, ['class' => 'form-control', 'style' => 'width: 100%; padding: 4px;', 'prompt' => '']); ?>
                                <?= (empty($field['unit']) ? '' : Html::tag('span', $field['unit'], ['class' => 'input-group-addon'])) ?>
                                <?php if ($filter['value'] !== null && false) : ?>
                                    <span class="input-group-btn">
                                        <button class="btn btn-danger" type="button" style="height: 34px;padding-top: 9px;">
                                            <span class="glyphicon glyphicon-trash" aria-hidden="true"></span>
                                        </button>
                                    </span>
                                <?php endif ?>
                            </div>
                        </div>
        <?php
                    endif;
                endif;

                $i++;
            endforeach;
        endforeach;
        ?>

    </div>

    <div class="row pb20">
        <div class="col-sm-6">
            <button type="submit" class="btn btn-default btn-block"><?= Yii::t('app', 'Search') ?></button>
        </div>
    </div>

    <?= Html::endForm(); ?>

<?php endif; ?>

<?php if ($this->context->id == 'site' && !in_array($this->context->action->id, ['category'])) : ?>
    <?php if (Blog::categories()) : ?>
        <div class="row pb20">
            <div class="col-sm-12">
                <div class="list-group text-center">
                    <?php
                    foreach (Blog::categories() as $id => $title) {
                        $url = Blog::url('site/category', ['id' => $id]);
                        echo '<a class="list-group-item list-group-item-action p-2" href="' . HtmlPurifier::process($url) . '"><h4 class="h4mainmenu">' . HtmlPurifier::process($title) . '</h4></a>';
                    }
                    ?>
                </div>
            </div>
        </div>
    <?php endif ?>
<?php endif ?>