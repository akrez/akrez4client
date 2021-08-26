<?php

use app\assets\cdn\CompressorJsAsset;
use app\assets\cdn\LeafletAsset;
use app\models\Blog;
use yii\web\View;
use yii\helpers\Url;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Html;
use yii\helpers\HtmlPurifier;

LeafletAsset::register($this);
CompressorJsAsset::register($this);

$latLng = [
    'lat' => ($model->lat ? doubleval($model->lat) : null),
    'lng' => ($model->lng ? doubleval($model->lng) : null),
];

$this->registerJs('
var messages = ' . json_encode([
    'compressor_error' => Yii::t('yii', 'The file "{file}" is not an image.', ['file' => $model->getAttributeLabel('receipt_file')])
]) . '
$("#invoice-receipt-handler").change(function(e) {
    var img = e.target.files[0];
    new Compressor(img, {
        quality: 1,
        maxWidth: 1600,
        maxHeight: 1600,
        success(result) {
            var reader = new FileReader();
            reader.readAsDataURL(result);
            reader.onloadend = function() {
                var base64data = reader.result;
                $("#invoice-receipt_file").val(base64data);
                $("#invoice-receipt-image").attr("src", base64data);
            }
        },
        error(err) {
            alert(messages.compressor_error);
        },

    });
});

var latLng = ' . json_encode($latLng) . ';
var map = L.map("map", {
    center: [0.00, 0.00],
    zoom: 13
});
var osmUrl = "http://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png";
var osmLayer = new L.TileLayer(osmUrl, {
    maxZoom: 19
});
map.addLayer(osmLayer);

var marker = L.marker([0.00, 0.00]).addTo(map);

function centerLeafletMapOnMarker(latLng) {
    marker.setLatLng(latLng);
    $("#invoice-lat").val(latLng.lat);
    $("#invoice-lng").val(latLng.lng);
}

function getCurrentLocation() {
    map.locate({
        setView: true,
        maxZoom: 14
    });
}

if (latLng.lat > 0 && latLng.lng > 0) {
    map.setView(latLng);
    map.setZoom(19);
    centerLeafletMapOnMarker(latLng);
} else {
    getCurrentLocation();
}

map.on("move", function() {
    centerLeafletMapOnMarker(map.getCenter());
});
map.on("dragend", function() {
    centerLeafletMapOnMarker(map.getCenter());
});

map.on("locationfound", function(e) {
    centerLeafletMapOnMarker(e.latlng);
});

$(document).on("click", ".found-location", function() {
    getCurrentLocation();
});


', View::POS_READY);
?>
<div class="row">
    <div class="col-sm-12">
        <?php
        $form = ActiveForm::begin([
            'action' => Url::current(),
            'fieldConfig' => [
                'template' => '<div class="input-group"><div class="input-group-prepend">{label}</div>{input}</div>{error}{hint}',
                'labelOptions' => [
                    'class' => 'input-group-text',
                ],
            ]
        ]);
        ?>
        <div class="row">
            <div class="col-sm-4">
                <?= $form->field($model, 'name')->textInput(); ?>
            </div>
            <div class="col-sm-4">
                <?= $form->field($model, 'mobile')->textInput(['placeholder' => '09101234567']); ?>
            </div>
            <div class="col-sm-4">
                <?= $form->field($model, 'phone')->textInput(['placeholder' => '02199876543']); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-5">
                <div class="row">
                    <div class="col-sm-12">
                        <?= $form->field($model, 'city')->dropdownList((array)Blog::getApiConstant(Blog::print('language'), ['city'])); ?>
                    </div>
                    <div class="col-sm-12">
                        <?= $form->field($model, 'postal_code')->textInput(['placeholder' => '1234512345']); ?>
                    </div>
                    <div class="col-sm-12">
                        <?= $form->field($model, 'address')->textarea(["rows" => "5"]); ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-7">
                <div id="map" style="width:100%; height:254px;">
                    <div class="btn-toolbar" style="left: 0;bottom: 0;position: absolute;margin: 10px;z-index: 9999;">
                        <div class="btn-group mr-2">
                            <button type="button" class="btn btn-light found-location"><i class="fas fa-map-marker-alt"></i></button>
                        </div>
                    </div>
                </div>
                <?php
                echo $form->field($model, 'lat', ['options' => ['class' => 'd-none']])->hiddenInput(['id' => 'invoice-lat']);
                echo $form->field($model, 'lng', ['options' => ['class' => 'd-none']])->hiddenInput(['id' => 'invoice-lng']);
                ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="alert alert-secondary" role="alert">
                    <div class="row">
                        <div class="col-sm-8">
                            <ul class="mb-0 pr-3">
                                <?php foreach (Blog::getBlogAccount() as $account) : ?>
                                    <li>
                                        <?= Blog::getConstant('blog_account_identity_type', $account['identity_type']) ?> <?= HtmlPurifier::process($account['name']) ?>
                                        <br />
                                        <span class="text-monospace"><?= HtmlPurifier::process($account['identity']) ?></span>
                                    </li>
                                <?php endforeach; ?>
                            </ul>
                            <label class="btn btn-info mt-3" for="invoice-receipt-handler"><?= Yii::t('app', 'upload payment receipt image') ?></label>
                            <?php
                            echo $form->field($model, 'receipt_file', ['options' => ['class' => 'd-none']])->hiddenInput();
                            ?>
                        </div>
                        <div class="col-sm-4">
                            <?= Html::tag('img', '', [
                                'id' => 'invoice-receipt-image',
                                'class' => 'img img-fluid',
                                'src' => ($model->receipt_file ? $model->receipt_file : false),
                            ]); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <?= $form->field($model, 'des')->textarea(); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <?= $form->errorSummary($model); ?>
            </div>
        </div>

        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <button type="submit" class="btn btn-primary"> <?= Yii::t('app', 'Invoice') ?> </button>
                </div>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
        <input type="file" id="invoice-receipt-handler" class="d-none" />
    </div>
</div>