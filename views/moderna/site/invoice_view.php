<?php

use app\assets\cdn\LeafletAsset;
use app\models\Blog;
use app\models\Invoice;
use app\models\Model;
use yii\bootstrap4\ActiveField;
use yii\bootstrap4\ActiveForm;
use yii\bootstrap4\Breadcrumbs;
use yii\bootstrap4\Html;
use yii\data\ArrayDataProvider;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\helpers\Html as HelpersHtml;
use yii\helpers\HtmlPurifier;

/* @var $this yii\web\View */
/* @var $searchModel app\models\InvoiceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

const IS_CUSTOMER = true;

function getAttributeLabelOfDelivery($attribute)
{
    $model = Model::instance();
    return $model->getAttributeLabel($attribute);
}

function getInvoiceValidStatuses()
{
    return Blog::getConstant('invoice_valid_statuses');
}

$this->title = Yii::t('app', 'View invoice');

$deliveries = ArrayHelper::index($deliveries, 'id');
$delivery = $deliveries[$invoice['delivery_id']];

LeafletAsset::register($this);

$this->registerCss("
");
?>

<style>
    .table td {
        vertical-align: middle !important;
        text-align: center;
    }

    .table-vertical-align-middle td,
    .table-vertical-align-middle thead th {
        vertical-align: middle;
        text-align: center;
    }

    .deprecated-card text-white {
        opacity: .58;
    }

    .deprecated-card:hover {
        opacity: 1;
    }

    .max-height-256 {
        max-height: 256px;
    }
</style>

<h1 class="mb-2"><?= Html::encode($this->title) ?></h1>

<div class="row mb-2">
    <div class="col-sm-12">
        <div class="btn-group btn-group-md d-flex" role="group">
            <?php
            foreach (getInvoiceValidStatuses() as $validStatusKey => $validStatus) {
                $validStatusBtnClass = 'btn-light';
                if ($validStatusKey < $invoice['status']) {
                    $validStatusBtnClass = 'btn-success';
                } elseif ($invoice['status'] == $validStatusKey) {
                    $validStatusBtnClass = 'btn-info';
                }
                echo '<div href="" class="btn ' . $validStatusBtnClass . ' w-100">' . $validStatus . '</div>';
            } ?>
        </div>
    </div>
</div>

<?= GridView::widget([
    'dataProvider' => new ArrayDataProvider([
        'allModels' => [$invoice],
        'modelClass' => 'app\models\Model',
        'sort' => false,
        'pagination' => false,
    ]),
    'filterModel' => null,
    'layout' => "{items}\n{pager}",
    'columns' => [
        [
            'attribute' => 'id',
        ],
        [
            'attribute' => 'status',
            'value' => function ($model, $key, $index, $grid) {
                if (isset($model['status'])) {
                    return Blog::getConstant('invoice_valid_statuses', $model['status']);
                }
                return '';
            },
        ],
        [
            'attribute' => 'created_at',
            'format' => 'datetimefa',
        ],
        [
            'attribute' => 'price',
            'format' => 'price',
        ],
        [
            'attribute' => 'carts_count',
        ],
        [
            'attribute' => 'des',
        ],
        [
            'attribute' => 'mobile',
            'value' => function ($model, $key, $index, $grid) use ($customer) {
                if (isset($customer['mobile'])) {
                    return $customer['mobile'];
                }
                return '';
            },
        ],
    ],
]); ?>

<?= GridView::widget([
    'dataProvider' => new ArrayDataProvider([
        'allModels' => $invoiceItems,
        'modelClass' => 'app\models\Model',
        'sort' => false,
        'pagination' => false,
    ]),
    'filterModel' => null,
    'columns' => [
        [
            'attribute' => 'code',
        ],
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
        [
            'attribute' => 'title',
        ],
        [
            'attribute' => 'color_code',
            'format' => 'raw',
            'value' => function ($model, $key, $index, $grid) {
                if ($model['color_code']) {
                    return '<span class="border border-dark rounded" style="background-color:' . $model['color_code'] . '">⠀⠀</span> ' . Blog::colorLabel($model['color_code']);
                }
                return '';
            },
        ],
        [
            'attribute' => 'guaranty',
        ],
        [
            'attribute' => 'des',
        ],
        [
            'attribute' => 'price',
            'format' => 'price',
        ],
        [
            'attribute' => 'cnt',
        ],
    ],
]); ?>

<hr>

<?php
$form = ActiveForm::begin([
    'options' => ['data-pjax' => true],
    'action' => Blog::url('site/invoice-view', ['state' => 'newMessage', 'id' => $invoice['id']]),
]);
?>
<div class="row mb-2">
    <div class="col-sm-10">
        <div class="input-group input-group-sm">
            <?= Html::activeInput('text', $invoiceMessageModel, 'message', [
                'class' => 'form-control',
            ]) ?>
            <div class="input-group-append">
                <?= Html::submitButton(' <i class="fas fa-caret-left"></i> ', [
                    'class' => 'btn btn-sm btn-success text-center',
                    'style' => 'min-width: 2.5rem',
                ]) ?>
            </div>
        </div>
    </div>
</div>
<?php ActiveForm::end(); ?>

<?php
$dataParts = [];
foreach ([
    'invoiceMessages' => $invoiceMessages,
    'invoiceStatuses' => $invoiceStatuses,
    'payments' => $payments,
    'deliveries' => $deliveries,
] as $dataKey => $dataArray) {
    foreach ($dataArray as $dataValue) {
        $dataParts[$dataValue['created_at']][$dataKey][$dataValue['id']] = $dataValue;
    }
}

krsort($dataParts);

foreach ($dataParts as $dataCreatedAt => $dataPart) {
    $dataCreatedAtFa = Yii::$app->formatter->asDatetimefa($dataCreatedAt);
    foreach ($dataPart as $dataPartName => $dataValues) {
        foreach ($dataValues as $dataValue) {
?>

            <?php if ($dataPartName == 'deliveries') { ?>
                <div class="card text-secondary bg-light mb-2 <?= ($dataValue['id'] == $invoice['delivery_id'] ? '' : 'deprecated-card') ?>">
                    <div class="card-header">
                        <i class="fas fa-map-pin"></i>
                        <small><?= $dataCreatedAtFa ?></small>
                    </div>
                    <?php
                    echo $this->render('_delivery_details', ['delivery' => $delivery]);
                    ?>
                </div>
            <?php } ?>

            <?php if ($dataPartName == 'payments') { ?>
                <div class="card text-secondary bg-light mb-2">
                    <div class="card-header">
                        <i class="fas fa-list-alt"></i>
                        <small><?= $dataCreatedAtFa ?></small>
                    </div>
                    <div class="card-body">
                        <a href="<?= Blog::getImage('payment', '_', $dataValue['payment_name']) ?>" target="_blank">
                            <img class="img img-responsive max-height-256" src="<?= Blog::getImage('payment', '256_256', $dataValue['payment_name']) ?>">
                        </a>
                    </div>
                </div>
            <?php } ?>

            <?php if ($dataPartName == 'invoiceStatuses') { ?>
                <div class="card text-white bg-info mb-2">
                    <div class="card-header">
                        <i class="far fa-bell"></i>
                        <small><?= $dataCreatedAtFa ?></small>
                        <br>
                        <?= $dataValue['message'] ?>
                    </div>
                </div>
            <?php } ?>

            <?php if ($dataPartName == 'invoiceMessages') {
                $isMineMessage = (IS_CUSTOMER == $dataValue['is_customer']);
            ?>
                <div class="row mb-2">
                    <?php if (!$isMineMessage) { ?>
                        <div class="col-sm-2">
                        </div>
                    <?php } ?>
                    <div class="col-sm-10">
                        <div class="card <?= (!$isMineMessage ? 'text-secondary bg-light' : 'text-white bg-success') ?>">
                            <div class="card-header">
                                <i class="fas fa-comment-alt"></i>
                                <small><?= $dataCreatedAtFa ?></small>
                            </div>
                            <div class="card-header text-secondary bg-light">
                                <?= HtmlPurifier::process($dataValue['message']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            <?php } ?>

<?php
        }
    }
}
?>