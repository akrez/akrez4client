<?php

namespace app\models;

use Yii;
use app\models\City;
use yii\helpers\Json;

/**
 * This is the model class for table "invoice".
 *
 * @property int $id
 * @property int|null $updated_at
 * @property int|null $created_at
 * @property int|null $status
 * @property float $price
 * @property int $carts_count
 * @property int|null $parent_delivery_id
 * @property int|null $delivery_at
 * @property int|null $payment_id
 * @property int|null $payment_at
 * @property string|null $params
 * @property string $blog_name
 * @property int $customer_id
 */
class Invoice extends Model
{
    public $parent_delivery_id;
    public $des;

    public function rules()
    {
        return [
            [['des'], 'string'],
            //
            [['parent_delivery_id'], 'required'],
            [['parent_delivery_id'], 'integer'],
        ];
    }
}
