<?php

namespace app\models;

use Yii;
use yii\helpers\Html;
use yii\helpers\Url;

class Blog extends Model
{
    public $created_at;
    public $name;
    public $title;
    public $slug;
    public $des;
    public $logo;
    public $email;
    public $facebook;
    public $phone;
    public $mobile;
    public $instagram;
    public $telegram;
    public $address;
    public $twitter;
    //
    public $categories = [];

    public function rules()
    {
        return [
            [['created_at', 'name', 'title', 'slug', 'des', 'logo', 'email', 'facebook', 'phone', 'mobile', 'instagram', 'telegram', 'address', 'twitter',], 'safe']
        ];
    }

    public static function name()
    {
        return self::print('name');
    }

    public static function print($attribute)
    {
        return Html::encode(Yii::$app->blog->{$attribute});
    }

    public static function url($action, $config = [], $scheme = false)
    {
        if (Yii::$app->params['isParked']) {
            return Url::to([0 => $action] + $config, $scheme);
        } else {
            return Url::to([0 => $action] + $config, $scheme);
        }
    }

    public static function firstPageUrl()
    {
        return self::url('site/index');
    }

    public static function categories()
    {
        return Yii::$app->blog->categories;
    }
}
