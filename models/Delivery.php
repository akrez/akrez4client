<?php

namespace app\models;

/**
 * This is the model class for table "delivery".
 *
 * @property int $id
 * @property int|null $updated_at
 * @property int|null $created_at
 * @property int|null $is_template
 * @property int|null $status
 * @property string|null $name
 * @property string|null $mobile
 * @property string|null $phone
 * @property string|null $params
 * @property string $blog_name
 * @property int $customer_id
 * @property int|null $invoice_id
 * @property int|null $parent_id
 */
class Delivery extends Model
{
    public $id;

    public $updated_at;
    public $created_at;
    public $status;
    public $name;
    public $mobile;
    public $phone;

    public $postal_code;
    public $city;
    public $address;
    public $lat;
    public $lng;
    public $des;

    public function rules()
    {
        return [
            [['name', 'mobile', 'phone', 'postal_code', 'city', 'address', 'lat', 'lng'], 'required'],
            [['lat',], 'match', 'pattern' => "/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/"],
            [['lng',], 'match', 'pattern' => "/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/"],
            [['name'], 'string', 'max' => 60],
            [['mobile',], 'match', 'pattern' => '/^09[0-9]{9,15}$/'],
            [['phone',], 'match', 'pattern' => "/^0[0-9]{8,23}$/"],
            [['des'], 'string'],
            [['postal_code',], 'match', 'pattern' => "/^(\d{10})$/"],
            [['address'], 'string'],
        ];
    }
}
