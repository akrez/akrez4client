<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "invoice_message".
 *
 * @property int $id
 * @property int|null $created_at
 * @property int|null $invoice_id
 * @property string|null $message
 * @property int|null $is_customer
 * @property string|null $blog_name
 *
 */
class InvoiceMessage extends Model
{
    public $id;
    public $created_at;
    public $invoice_id;
    public $message;
    public $is_customer;
    public $blog_name;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['created_at', 'invoice_id'], 'integer'],
            [['message'], 'safe'],
            [['blog_name'], 'string', 'max' => 31],
            //
            [['message'], 'required'],
            [['is_customer'], 'boolean'],
        ];
    }
}
