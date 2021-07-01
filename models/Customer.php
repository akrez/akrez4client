<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%customer}}".
 *
 * @property int $id
 * @property int|null $updated_at
 * @property int|null $created_at
 * @property int $status
 * @property string|null $token
 * @property string|null $password_hash
 * @property string|null $verify_token
 * @property int|null $verify_at
 * @property string|null $reset_token
 * @property int|null $reset_at
 * @property string|null $mobile
 * @property string|null $name
 * @property string|null $params
 * @property string $blog_name
 */
class Customer extends \yii\db\ActiveRecord
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
            [['updated_at', 'created_at', 'status', 'verify_at', 'reset_at'], 'integer'],
            [['status', 'blog_name'], 'required'],
            [['params'], 'string'],
            [['token'], 'string', 'max' => 32],
            [['password_hash'], 'string', 'max' => 255],
            [['verify_token', 'reset_token'], 'string', 'max' => 11],
            [['mobile'], 'string', 'max' => 15],
            [['name', 'blog_name'], 'string', 'max' => 60],
            //signup
            [0 => ['mobile',], 1 => 'required', 'on' => 'signup',],
            [0 => ['mobile',], 1 => 'match', 'pattern' => '/^09[0-9]{9}$/', 'on' => 'signup',],
            [0 => ['password',], 1 => 'required', 'on' => 'signup',],
            [0 => ['password',], 1 => 'minLenValidation', 'params' => ['min' => 6,], 'on' => 'signup',],
            //signin
            [0 => ['mobile',], 1 => 'required', 'on' => 'signin',],
            [0 => ['mobile',], 1 => 'match', 'pattern' => '/^09[0-9]{9}$/', 'on' => 'signin',],
            [0 => ['password',], 1 => 'required', 'on' => 'signin',],
            [0 => ['password',], 1 => 'minLenValidation', 'params' => ['min' => 6,], 'on' => 'signin',],
            //resetPasswordRequest
            [0 => ['mobile',], 1 => 'required', 'on' => 'resetPasswordRequest',],
            [0 => ['mobile',], 1 => 'resetPasswordRequestValidation', 'on' => 'resetPasswordRequest',],
            [0 => ['mobile',], 1 => 'match', 'pattern' => '/^09[0-9]{9}$/', 'on' => 'resetPasswordRequest',],
            //resetPassword
            [0 => ['mobile',], 1 => 'required', 'on' => 'resetPassword',],
            [0 => ['mobile',], 1 => 'match', 'pattern' => '/^09[0-9]{9}$/', 'on' => 'resetPassword',],
            [0 => ['password',], 1 => 'required', 'on' => 'resetPassword',],
            [0 => ['password',], 1 => 'minLenValidation', 'params' => ['min' => 6,], 'on' => 'resetPassword',],
            [0 => ['reset_token',], 1 => 'required', 'on' => 'resetPassword',],
            //verify
            [0 => ['mobile',], 1 => 'required', 'on' => 'verify',],
            [0 => ['mobile',], 1 => 'match', 'pattern' => '/^09[0-9]{9}$/', 'on' => 'verify',],
            [0 => ['verify_token',], 1 => 'required', 'on' => 'verify',],
            //verifyRequest
            [0 => ['mobile',], 1 => 'required', 'on' => 'verifyRequest',],
            [0 => ['mobile',], 1 => 'match', 'pattern' => '/^09[0-9]{9}$/', 'on' => 'verifyRequest',],
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

    public static function getIdentityToken()
    {
        return self::getIdentityAttribute('token', false);
    }

    public static function getIdentityAttribute($attribute, $encode = true)
    {
        $value = null;
        if (Yii::$app->user->getIdentity()) {
            $value = Yii::$app->user->getIdentity()->$attribute;
            if ($encode) {
                $value = Html::encode($value);
            }
        }
        return $value;
    }

    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'updated_at' => Yii::t('app', 'Updated At'),
            'created_at' => Yii::t('app', 'Created At'),
            'status' => Yii::t('app', 'Status'),
            'token' => Yii::t('app', 'Token'),
            'password_hash' => Yii::t('app', 'Password Hash'),
            'verify_token' => Yii::t('app', 'Verify Token'),
            'verify_at' => Yii::t('app', 'Verify At'),
            'reset_token' => Yii::t('app', 'Reset Token'),
            'reset_at' => Yii::t('app', 'Reset At'),
            'mobile' => Yii::t('app', 'Mobile'),
            'name' => Yii::t('app', 'Name'),
            'params' => Yii::t('app', 'Params'),
            'blog_name' => Yii::t('app', 'Blog Name'),
        ];
    }
}
