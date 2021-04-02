<?php

namespace app\models;

use app\components\Http;
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
    //
    public static $data = null;

    public static function setData($data)
    {
        self::$data = $data;
    }

    public static function getData(...$levels)
    {
        $value = self::$data;
        foreach ($levels as $level) {
            if (isset($value[$level])) {
                $value = $value[$level];
            } else {
                return null;
            }
        }
        return $value;
    }

    public static function removeData(...$levels)
    {
        $value = self::$data;
        foreach ($levels as $level) {
            if (isset($value[$level])) {
                $value = $value[$level];
            } else {
                return null;
            }
        }
        return $value;
    }
    //
    public static $constant = null;

    public static function setConstant($constant)
    {
        self::$constant = $constant;
    }

    public static function getConstant(...$levels)
    {
        $value = self::$constant;
        foreach ($levels as $level) {
            if (isset($value[$level])) {
                $value = $value[$level];
            } else {
                return null;
            }
        }
        return $value;
    }
    //
    public static function getImage($type, $whq, $name)
    {
        $type = preg_replace('/[^A-Za-z0-9\-\.\_]/', '', $type);
        $whq = preg_replace('/[^A-Za-z0-9\-\.\_]/', '', $whq);
        $name = preg_replace('/[^A-Za-z0-9\-\.\_]/', '', $name);
        //
        $basePath = Yii::getAlias("@webroot/gallery/$type/$whq");
        $path = "$basePath/$name";
        $url = Yii::getAlias("@web") . "/gallery/$type/$whq/$name";
        //
        if (file_exists($path)) {
            return $url;
        }
        file_exists($basePath) || mkdir($basePath, '755', true);
        if (Http::downloadImage($type, $path, $name)) {
            return $url;
        }
    }
    //
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

    //

    public static function normalizeArray($arr, $returnAsArray = false, $glue = ",")
    {
        $arr = self::normalizeArrayUnorder($arr, true, $glue);
        sort($arr);
        if ($returnAsArray) {
            return $arr;
        }
        return implode($glue, $arr);
    }

    public static function normalizeArrayUnorder($arr, $returnAsArray = false, $glue = ",")
    {
        if (is_array($arr)) {
            $arr = implode(",", $arr);
        }
        $arr = str_ireplace("\n", ",", $arr);
        $arr = str_ireplace(",", ",", $arr);
        $arr = str_ireplace("،", ",", $arr);
        $arr = explode(",", $arr);
        $arr = array_map("trim", $arr);
        $arr = array_unique($arr);
        $arr = array_filter($arr);
        if ($returnAsArray) {
            return $arr;
        }
        return implode($glue, $arr);
    }

    public static function filterArray($arr, $doFilter = true, $checkUnique = true, $doTrim = true)
    {
        if ($doTrim) {
            $arr = array_map('trim', $arr);
        }
        if ($checkUnique) {
            $arr = array_unique($arr);
        }
        if ($doFilter) {
            $arr = array_filter($arr, 'strlen');
        }
        return $arr;
    }
}
