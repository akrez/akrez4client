<?php

namespace app\models;

use Yii;
use yii\web\IdentityInterface;

/**
 * This is the model class for table "{{%Customer}}".
 *
 * @property int $id
 * @property int|null $created_at
 * @property int|null $updated_at
 * @property string $status
 * @property string|null $token
 * @property string|null $password_hash
 * @property string|null $reset_token
 * @property int|null $reset_at
 * @property string|null $email
 * @property string $blog_name
 */
class Customer extends Model implements IdentityInterface
{

    public $password;

    public static function tableName()
    {
        return '{{%customer}}';
    }

    public function rules()
    {

        return [
            //
            [0 => ['email', 'updated_at', 'created_at', 'status', 'token', 'blog_name',], 1 => 'safe',],
            //signup
            [0 => ['email',], 1 => 'required', 'on' => 'signup',],
            [0 => ['email',], 1 => 'email', 'on' => 'signup',],
            [0 => ['password',], 1 => 'required', 'on' => 'signup',],
            [0 => ['password',], 1 => 'minLenValidation', 'params' => ['min' => 6,], 'on' => 'signup',],
            //signin
            [0 => ['email',], 1 => 'required', 'on' => 'signin',],
            [0 => ['email',], 1 => 'email', 'on' => 'signin',],
            [0 => ['password',], 1 => 'required', 'on' => 'signin',],
            [0 => ['password',], 1 => 'minLenValidation', 'params' => ['min' => 6,], 'on' => 'signin',],
            //resetPasswordRequest
            [0 => ['email',], 1 => 'required', 'on' => 'resetPasswordRequest',],
            [0 => ['email',], 1 => 'email', 'on' => 'resetPasswordRequest',],
            //resetPassword
            [0 => ['email',], 1 => 'required', 'on' => 'resetPassword',],
            [0 => ['email',], 1 => 'email', 'on' => 'resetPassword',],
            [0 => ['password',], 1 => 'required', 'on' => 'resetPassword',],
            [0 => ['password',], 1 => 'minLenValidation', 'params' => ['min' => 6,], 'on' => 'resetPassword',],
            [0 => ['reset_token',], 1 => 'required', 'on' => 'resetPassword',],
        ];
    }

    /////

    public static function findIdentity($id)
    {
        return static::find()->where(['id' => $id])->one();
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return static::find()->where(['token' => $token])->one();
    }

    public function getId()
    {
        return $this->getPrimaryKey();
    }

    public function getAuthKey()
    {
        return $this->token;
    }

    public function validateAuthKey($authKey)
    {
        return $this->getAuthKey() === $authKey;
    }

    /////


    public function minLenValidation($attribute, $params, $validator)
    {
        $min = $params['min'];
        if (strlen($this->$attribute) < $min) {
            $this->addError($attribute, Yii::t('yii', '{attribute} must be no less than {min}.', ['min' => $min, 'attribute' => $this->getAttributeLabel($attribute)]));
        }
    }

    public function maxLenValidation($attribute, $params, $validator)
    {
        $max = $params['max'];
        if ($max < strlen($this->$attribute)) {
            $this->addError($attribute, Yii::t('yii', '{attribute} must be no greater than {max}.', ['max' => $max, 'attribute' => $this->getAttributeLabel($attribute)]));
        }
    }

    public function getCustomer()
    {
        return $this->_customer;
    }
}
