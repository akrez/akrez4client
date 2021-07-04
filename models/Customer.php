<?php

namespace app\models;

use Yii;
use yii\helpers\HtmlPurifier;
use yii\web\IdentityInterface;

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
class Customer extends ActiveRecord implements IdentityInterface
{
    public $password;
    public $captcha;

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
            [['mobile',], 'required', 'on' => 'signup',],
            [['mobile',], 'match', 'pattern' => '/^09[0-9]{9}$/', 'on' => 'signup',],
            [['password',], 'required', 'on' => 'signup',],
            [['password',], 'string', 'min' => 6, 'strict' => false, 'on' => 'signup',],
            [['captcha',], 'required', 'on' => 'signup',],
            [['captcha',], 'captcha', 'on' => 'signup',],
            //signin
            [['mobile',], 'required', 'on' => 'signin',],
            [['mobile',], 'match', 'pattern' => '/^09[0-9]{9}$/', 'on' => 'signin',],
            [['password',], 'required', 'on' => 'signin',],
            [['password',], 'string', 'min' => 6, 'strict' => false, 'on' => 'signin',],
            [['captcha',], 'required', 'on' => 'signin',],
            [['captcha',], 'captcha', 'on' => 'signin',],
            //resetPasswordRequest
            [['mobile',], 'required', 'on' => 'resetPasswordRequest',],
            [['mobile',], 'match', 'pattern' => '/^09[0-9]{9}$/', 'on' => 'resetPasswordRequest',],
            //resetPassword
            [['mobile',], 'required', 'on' => 'resetPassword',],
            [['mobile',], 'match', 'pattern' => '/^09[0-9]{9}$/', 'on' => 'resetPassword',],
            [['password',], 'required', 'on' => 'resetPassword',],
            [['password',], 'string', 'min' => 6, 'strict' => false, 'on' => 'resetPassword',],
            [['reset_token',], 'required', 'on' => 'resetPassword',],
            //verify
            [['mobile',], 'required', 'on' => 'verify',],
            [['mobile',], 'match', 'pattern' => '/^09[0-9]{9}$/', 'on' => 'verify',],
            [['verify_token',], 'required', 'on' => 'verify',],
            //verifyRequest
            [['mobile',], 'required', 'on' => 'verifyRequest',],
            [['mobile',], 'match', 'pattern' => '/^09[0-9]{9}$/', 'on' => 'verifyRequest',],
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

    public static function getNewSignupModel()
    {
        return new Customer(['scenario' => 'signup']);
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
            //
            'password' => Yii::t('app', 'Password'),
            'captcha' => Yii::t('app', 'Captcha'),
        ];
    }

    public static function print($attribute)
    {
        $user = Yii::$app->user->getIdentity();
        if ($user) {
            return HtmlPurifier::process($user->{$attribute});
        }
        return null;
    }
}
