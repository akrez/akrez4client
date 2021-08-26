<?php

use app\assets\cdn\LeafletAsset;
use app\models\Blog;
use app\models\Invoice;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Html;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\HtmlPurifier;

$this->title = Yii::t('app', 'View invoice');

$invoice = new Invoice();
$invoice->setScenario('view');
$invoice->load(Blog::getData('invoice'), '');

LeafletAsset::register($this);

$this->registerCss("
.table-vertical-align-middle td,
.table-vertical-align-middle thead th {
    vertical-align: middle;
    text-align: center;
}
");

$this->registerJs('
var latLng = ' . json_encode([$invoice->lat,  $invoice->lng,]) . ';
var map = L.map("map", {
    center: latLng,
    zoom: 14
});
//map.dragging.disable();
var osmUrl = "http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png";
var osmLayer = new L.TileLayer(osmUrl, {
    maxZoom: 19
});
map.addLayer(osmLayer);

var marker = L.marker(latLng).addTo(map);
map.on("move", function() {
    marker.setLatLng(latLng);
});
map.on("dragend", function() {
    marker.setLatLng(latLng);
});
');
?>

<?=
Breadcrumbs::widget([
    'homeLink' => [
        'label' => Yii::t('yii', 'Home'),
        'url' => Blog::firstPageUrl(),
    ],
    'links' => [
        ['label' => Yii::t('app', 'Invoices'), 'url' => Blog::url('site/invoices')],
        ['label' => $this->title],
    ],
]);
?>

<div class="row">
    <div class="col-sm-12 pb-2">
        <h1><?= $this->title ?></h1>
    </div>
</div>

<div class="row">
    <div class="col-sm-12">
        <?php
        $dataProvider = new ArrayDataProvider([
            'allModels' => Blog::getData('invoiceItems'),
            'modelClass' => 'app\models\Model',
            'sort' => false,
            'pagination' => false,
        ]);
        ?>
        <div class="table-responsive">
            <?=
            GridView::widget([
                'dataProvider' => $dataProvider,
                'headerRowOptions' => [
                    'class' => 'thead-dark',
                ],
                'tableOptions' => [
                    'class' => 'table table-striped table-bordered table-sm table-hover table-vertical-align-middle',
                ],
                'columns' => [
                    'code',
                    [
                        'attribute' => 'image',
                        'format' => 'raw',
                        'value' => function ($model, $key, $index, $grid) {
                            if ($model['image']) {
                                return Html::img(Blog::getImage('product', '400', $model['image']), [
                                    "style" => "max-height: 51px;",
                                ]);
                            }
                            return '';
                        },
                    ],
                    'title',
                    [
                        'attribute' => 'image',
                        'format' => 'raw',
                        'value' => function ($model, $key, $index, $grid) {
                            if ($model['color_code']) {
                                return '<span class="border border-dark rounded" style="background-color:' . $model['color_code'] . '">⠀⠀</span> ' . Blog::colorLabel($model['color_code']);
                            }
                            return '';
                        },
                    ],
                    'guaranty',
                    'des',
                    'price:price',
                    'cnt',
                ],
            ]);
            ?>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-sm-12 pb20">
        <table class="table table-bordered table-sm table-hover">
            <tbody>
                <tr>
                    <td class="table-active"><?= $invoice->getAttributeLabel('name') ?></td>
                    <td colspan="3"><?= HtmlPurifier::process($invoice->name) ?></td>
                    <td class="table-active"><?= $invoice->getAttributeLabel('updated_at') ?></td>
                    <td><?= Yii::$app->formatter->asDatetimefa($invoice->updated_at) ?></td>
                    <td class="table-active"><?= $invoice->getAttributeLabel('created_at') ?></td>
                    <td><?= Yii::$app->formatter->asDatetimefa($invoice->created_at) ?></td>
                </tr>
                <tr>
                    <td class="table-active"><?= $invoice->getAttributeLabel('phone') ?></td>
                    <td><?= HtmlPurifier::process($invoice->phone) ?></td>
                    <td class="table-active"><?= $invoice->getAttributeLabel('mobile') ?></td>
                    <td><?= HtmlPurifier::process($invoice->mobile) ?></td>
                    <td colspan="4" rowspan="6" style="height: inherit;position: relative;">
                        <div id="map" style="position: absolute;top: 0;bottom: 0;right: 0;left: 0;"></div>
                    </td>
                </tr>
                <tr>
                    <td class="table-active"><?= $invoice->getAttributeLabel('city') ?></td>
                    <td><?= Blog::getConstant('city', $invoice->city) ?></td>
                    <td class="table-active"><?= $invoice->getAttributeLabel('postal_code') ?></td>
                    <td><?= HtmlPurifier::process($invoice->postal_code) ?></td>
                </tr>
                <tr>
                    <td class="table-active"><?= $invoice->getAttributeLabel('address') ?></td>
                    <td colspan="3"><?= HtmlPurifier::process($invoice->address) ?></td>
                </tr>
                <tr>
                    <td class="table-active"><?= $invoice->getAttributeLabel('des') ?></td>
                    <td colspan="3"><?= HtmlPurifier::process($invoice->des) ?></td>
                </tr>
                <tr>
                    <td class="table-active"><?= $invoice->getAttributeLabel('price') ?></td>
                    <td colspan="3"><?= Yii::$app->formatter->asPrice($invoice->price) ?></td>
                </tr>
                <tr>
                    <td class="table-active"><?= $invoice->getAttributeLabel('receipt') ?></td>
                    <td colspan="3" class="text-center">
                        <?php
                        $src = Blog::getImage('receipt', '_', $invoice->receipt);
                        $img = Html::img($src, [
                            "style" => "max-height: 75px;",
                        ]);
                        echo Html::a($img, $src, ['target' => '_blank']);
                        ?>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</div>