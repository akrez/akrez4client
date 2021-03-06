<?php

namespace app\models;

/**
 * This is the model class for table "invoice".
 *
 * @property int $id
 * @property int|null $updated_at
 * @property int|null $created_at
 * @property int|null $status
 * @property string|null $name
 * @property string|null $mobile
 * @property string|null $phone
 * @property float $price
 * @property int $carts_count
 * @property string|null $params
 * @property string $blog_name
 * @property int $customer_id
 */
class Invoice extends Model
{
    public $id;
    public $updated_at;
    public $created_at;
    public $name;
    public $mobile;
    public $phone;
    public $postal_code;
    public $city;
    public $address;
    public $lat;
    public $lng;
    public $des;
    public $receipt;
    public $receipt_file;
    //
    public $status;
    public $customer_id;
    public $blog_name;
    //
    public $price;
    public $carts_count;

    public function rules()
    {
        return [
            [['name', 'mobile', 'phone', 'postal_code', 'city', 'address', 'lat', 'lng', 'receipt_file',], 'required'],
            [['name'], 'string', 'max' => 60],
            [['mobile',], 'match', 'pattern' => '/^09[0-9]{9,15}$/'],
            [['phone',], 'match', 'pattern' => "/^0[0-9]{8,23}$/"],
            [['des'], 'string'],
            [['postal_code',], 'match', 'pattern' => "/^(\d{10})$/"],
            [['city'], 'string'],
            [['address'], 'string'],
            [['lat',], 'match', 'pattern' => "/^[-]?(([0-8]?[0-9])\.(\d+))|(90(\.0+)?)$/"],
            [['lng',], 'match', 'pattern' => "/^[-]?((((1[0-7][0-9])|([0-9]?[0-9]))\.(\d+))|180(\.0+)?)$/"],
            [['receipt_file'], 'string', 'strict' => false,],
            //
            [['id', 'updated_at', 'created_at', 'name', 'mobile', 'phone', 'postal_code', 'city', 'address', 'lat', 'lng', 'des', 'status', 'customer_id', 'blog_name', 'price', 'carts_count', 'receipt',], 'safe', 'on' => 'view',],
        ];
    }
}
