<?php

namespace app\assets;

use yii\web\AssetBundle;

class ModernaAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'themes/moderna/css/moderna.css',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'app\assets\cdn\Bootstrap4Asset',
        'app\assets\cdn\FontSahelAsset',
        'app\assets\cdn\FontawesomeAsset',
    ];
}
