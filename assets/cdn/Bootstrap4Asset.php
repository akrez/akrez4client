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
class Bootstrap4Asset extends AssetBundle
{
    public $sourcePath = '@npm/bootstrap/dist';
    public $css = [
        YII_ENV === 'prod' ? 'css/bootstrap.min.css' : 'css/bootstrap.css',
    ];
    public $js = [
        YII_ENV === 'prod' ? 'js/bootstrap.bundle.min.js' : 'js/bootstrap.bundle.js',
    ];
}
