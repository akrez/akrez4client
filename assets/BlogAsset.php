<?php
namespace app\assets;

use yii\web\AssetBundle;

class BlogAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'themes/blog/css/blog.css',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'app\assets\cdn\BootstrapAsset',
        'app\assets\cdn\FontSahelFdAsset',
        //'yii\bootstrap\BootstrapThemeAsset',
    ];
}
