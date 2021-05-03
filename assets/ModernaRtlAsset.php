<?php

namespace app\assets;

use yii\web\AssetBundle;

class ModernaRtlAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $css = [
        'themes/moderna/css/moderna.css',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'app\assets\cdn\Bootstrap4RtlAsset',
        'app\assets\cdn\FontSahelFdAsset',
        'app\assets\cdn\FontawesomeAsset',
    ];
}
