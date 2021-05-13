<?php

namespace app\models;

use app\components\Http;
use app\components\Image;
use Yii;
use yii\helpers\Html;
use yii\helpers\HtmlPurifier;
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
    public $language;
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
        $language = Yii::$app->language;

        if (isset(self::$constant['constant'][$language])) {
            $value = self::$constant['constant'][$language];
        } else {
            return null;
        }

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
    public static function getImage($type, $whqm, $name)
    {
        $default = '';

        $type = preg_replace('/[^A-Za-z0-9\-\.\_]/', '', $type);
        $whqm = preg_replace('/[^A-Za-z0-9\-\.\_]/', '', $whqm);
        $name = preg_replace('/[^A-Za-z0-9\-\.\_]/', '', $name);

        $imagePath = Yii::getAlias("@webroot") . "/gallery/$type/$whqm/$name";
        $imageUrl = Yii::getAlias("@web") . "/gallery/$type/$whqm/$name";

        if (file_exists($imagePath)) {
            return $imageUrl;
        }

        $imageDirectory = dirname($imagePath);
        file_exists($imageDirectory) || mkdir($imageDirectory, '755', true);

        $originalPath = Yii::getAlias("@webroot") . "/gallery/$type/$name";
        $originalUrl = Yii::getAlias("@web") . "/gallery/$type/$name";

        if (file_exists($originalPath) || Http::downloadImage($type, $originalPath, $name)) {
            $whqm = explode('_', $whqm) + [0, 0, 0, 0];
            $whqm = [
                0 => intval($whqm[0]),
                1 => intval($whqm[1]),
                2 => intval($whqm[2]),
                3 => intval($whqm[3]),
            ];
            $handler = new Image();
            if ($handler->save($originalPath, $imagePath, $whqm[0], $whqm[1], $whqm[2], $whqm[3])) {
                return $imageUrl;
            }
            return $originalUrl;
        }
        return $default;
    }
    //
    public function rules()
    {
        return [
            [['created_at', 'name', 'title', 'slug', 'des', 'logo', 'email', 'facebook', 'phone', 'mobile', 'instagram', 'telegram', 'address', 'twitter', 'language'], 'safe']
        ];
    }

    public static function name()
    {
        return self::print('name');
    }

    public static function print($attribute)
    {
        return HtmlPurifier::process(Yii::$app->blog->{$attribute});
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

    public static function categories($categoryId = null)
    {
        if ($categoryId === null) {
            return self::getData('_categories');
        }
        return self::getData('_categories', $categoryId);
    }

    public static function isRtl()
    {
        return in_array(Yii::$app->language, ['fa-IR']);
    }

    public static function getMetaKeyword()
    {
        /////
        $blogName = Blog::print('name');
        $blogTitle = Blog::print('title');
        $blogSlug = Blog::print('slug');
        //
        $categories = (array)Blog::getData('_categories');
        $categoryId = Blog::getData('categoryId');
        $category = isset($categories[$categoryId]) ? $categories[$categoryId] : null;
        //
        $productTitle = Blog::getData('product', 'title');
        /////
        $words = [
            'نمایندگی فروش',
            'فروش',
            'خرید اینترنتی محصولات',
            'فروشگاه',
            'فروشگاه اینترنتی',
            'فروشگاه آنلاین',
            'خرید آنلاین'
        ];
        /////
        $keywords = [$blogTitle, $blogName, $blogTitle . '-' . $blogName, $blogTitle . '-' . $blogSlug];
        //
        if ($productTitle) {
            $keywords = array_merge($keywords, [
                $productTitle . ' ' . $blogTitle,
                $productTitle,
            ]);
        }
        //
        if ($category) {
            $keywords = array_merge($keywords, [$category, $category . ' ' . $blogTitle,], array_map(function ($value) use ($category) {
                return $value . ' ' . $category;
            }, $words));
        }
        //
        if ($categories) {
            $keywords = array_merge($keywords, $categories);
        }
        //
        if ($blogTitle) {
            $keywords = array_merge($keywords, array_map(function ($value) use ($blogTitle) {
                return $value . ' ' . $blogTitle;
            }, $words));
        }
        //
        return implode(',', $keywords);
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
