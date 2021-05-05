<?php

/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets\cdn;

use yii\web\AssetBundle;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class Bootstrap4RtlAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        YII_ENV === 'prod' ? 'cdn/css/bootstrap4-rtl.min.css' : 'cdn/css/bootstrap4-rtl.css',
    ];
    public $js = [
        'cdn/js/bootstrap.bundle.min.js',
    ];
}
