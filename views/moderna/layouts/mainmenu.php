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
    $(document).on("click", ".card-akrez > .card-header", function (e) {
        var header = $(this);
        var body = header.parents(".card").find(".card-body");
        var checkbox = header.find("input[type=checkbox]").first();
        if (checkbox) {
            var checked = !$(checkbox).is(":checked");
            checkbox.prop("checked", checked);
            if (checked) {
                body.collapse("show");
            } else {
                body.collapse("hide");
            }
        }
    });

', View::POS_READY);
$this->registerCss('
    .slider {
        width: calc(100% - 20px) !important;
    }
    .card-akrez .card-header {
        cursor: pointer;
        user-select: none;

        font-weight: 400;
        display: block;

        color: #555555;
        background-color: #eeeeee;
    }
    .card-akrez .card-body {
        max-height: 170px;
        overflow-y: auto;
    }
    .card-akrez .card-header > input[type=checkbox] {
        display: inline-block;
    }
');
?>

<?php if ($this->context->id == 'site' && in_array($this->context->action->id, ['category']) == false) : ?>
    <div class="row pb-2">
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

    <div class="row pt-2 pb-2">
        <div class="col-sm-12">
            <h4><?= Yii::t('app', 'Search') ?></h4>
        </div>
    </div>

    <?= Html::beginForm(Blog::url('site/category', ['id' => Blog::getData('categoryId')]), 'GET'); ?>

    <div class="row">

        <?php
        $section = 'Product';
        $fieldId = 'title';
        echo $this->render('/site/_widgets', [
            'section' => $section,
            'searchParams' => (array)Blog::getData($section, $fieldId),
            'field' => [
                "id" => null,
                "title" => $fieldId,
                "seq" => null,
                "in_summary" => 1,
                "category_id" => null,
                "unit" => null,
                "label_no" => null,
                "label_yes" => null,
                "widgets" => ['LIKE'],
                "label" => Yii::t('app', 'Title'),
                "options" => [],
            ],
        ]);

        $priceMin = Blog::getData('category', 'price_min');
        $priceMax = Blog::getData('category', 'price_max');
        if ($priceMin && $priceMax) {
            $section = 'Package';
            $fieldId = 'price';
            echo $this->render('/site/_widgets', [
                'section' => $section,
                'searchParams' => (array)Blog::getData($section, $fieldId),
                'field' => [
                    "id" => null,
                    "title" => $fieldId,
                    "seq" => null,
                    "in_summary" => 1,
                    "category_id" => null,
                    "unit" => "ریال",
                    "label_no" => null,
                    "label_yes" => null,
                    "widgets" => ['BETWEEN'],
                    "label" => Yii::t('app', 'Price'),
                    "options" => [$priceMin => null, $priceMax => null,],
                ],
            ]);
        }

        foreach ((array)Blog::getData('fields') as $fieldId => $field) : ///
            $section = 'ProductField';
            echo $this->render('/site/_widgets', [
                'section' => $section,
                'searchParams' => (array)Blog::getData($section, $fieldId),
                'field' => $field + [
                    'label' => $field['title'],
                    'options' => (array)Blog::getData('options', $field['title']),
                ],
            ]);
        endforeach;
        ?>

    </div>

    <div class="row pb-2">
        <div class="col-sm-6">
            <button type="submit" class="btn btn-secondary btn-block"><?= Yii::t('app', 'Search') ?></button>
        </div>
    </div>

    <?= Html::endForm(); ?>

<?php endif; ?>

<?php if ($this->context->id == 'site' && !in_array($this->context->action->id, ['category'])) : ?>
    <?php if (Blog::categories()) : ?>
        <div class="row pb-2">
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