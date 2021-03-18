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
    public $id;
    public $created_at;
    public $updated_at;
    public $status;
    public $token;
    public $password_hash;
    public $reset_token;
    public $reset_at;
    public $email;
    public $blog_name;
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
        return null;
    }

    public static function findIdentityByAccessToken($token, $type = null)
    {
        return null;
    }

    public function getId()
    {
        return $this->id;
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

    public static function getToken()
    {
        $_token = Yii::$app->session->get(Yii::$app->user->authKeyParam);
        if ($_token) {
            return $_token;
        }
        return null;
    }

    public static function setToken($token)
    {
        if (Yii::$app->session->has(Yii::$app->user->authKeyParam)) {
            self::removeToken();
        }
        Yii::$app->session->set(Yii::$app->user->authKeyParam, $token);
        return self::getToken();
    }

    public static function removeToken()
    {
        return Yii::$app->session->remove(Yii::$app->user->authKeyParam);
    }

    public function signout()
    {
        Yii::$app->user->logout();
        self::removeToken();
    }
}
