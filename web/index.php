<?php

function vd(...$input)
{
    foreach ($input as $i) {
        var_dump($i);
    }
}

function v(...$input)
{
    foreach ($input as $i) {
        var_dump($i);
    }
    die;
}

function jd(...$input)
{
    die(json_encode($input));
}

function ed($input)
{
    var_export($input);
    die;
}

ini_set('allow_url_fopen', 1);

defined('YII_DEBUG') or define('YII_DEBUG', true);
defined('YII_ENV') or define('YII_ENV', 'dev');
defined('BASE_PATH') or define('BASE_PATH', dirname(__DIR__));
defined('VENDOR_PATH') or define('VENDOR_PATH', BASE_PATH . '/vendor');
defined('APP_NAME') or define('APP_NAME', 'اکــرز');

require VENDOR_PATH . '/autoload.php';
require VENDOR_PATH . '/yiisoft/yii2/Yii.php';

$params = require(__DIR__ . '/../config/params.php');

$rules = [
    '/' => 'site/index',
    '/robots.txt' => 'site/robots',
    '/sitemap.xml' => 'site/sitemap',
    '/manifest.json' => 'site/manifest',
    '/gallery/<type:\w+>/<whq>/<name:[\w\.]+>' => 'site/gallery',
    '/<action:[\w\-]+>/<id>' => 'site/<action>',
    '/<action:[\w\-]+>' => 'site/<action>',
];

$config = [
    'id' => 'basic',
    'name' => APP_NAME,
    'basePath' => BASE_PATH,
    'language' => 'fa-IR',
    'controllerNamespace' => 'app\controllers',
    'bootstrap' => [
        'log',
    ],
    'vendorPath' => VENDOR_PATH,
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
    ],
    'components' => [
        'assetManager' => [
            'class' => 'yii\web\AssetManager',
            'bundles' => [
                'yii\web\JqueryAsset' => [
                    'js' => [
                        YII_ENV !== 'prod' ? 'jquery.js' : 'jquery.min.js'
                    ]
                ],
                'yii\bootstrap\BootstrapAsset' => [
                    'css' => [
                        YII_ENV !== 'prod' ? 'css/bootstrap.css' : 'css/bootstrap.min.css',
                    ]
                ],
                'yii\bootstrap\BootstrapPluginAsset' => [
                    'js' => [
                        YII_ENV !== 'prod' ? 'js/bootstrap.js' : 'js/bootstrap.min.js',
                    ]
                ],
                'yii\bootstrap4\BootstrapPluginAsset' => [
                    'js' => [],
                ],
                'yii\bootstrap4\BootstrapAsset' => [
                    'css' => [],
                ]
            ],
        ],
        'db' => $params['db'],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'i18n' => [
            'translations' => [
                'app' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                ],
            ],
        ],
        'request' => [
            'csrfParam' => '_csrf-blog-' . $params['params']['blogName'],
            'cookieValidationKey' => $params['cookieValidationKey'],
            'baseUrl' => $params['baseUrl'],
        ],
        'session' => [
            'name' => 'basic-blog-' . $params['params']['blogName'],
            'cookieParams' => [
                'httpOnly' => true,
            ],
        ],
        'user' => [
            'class' => 'yii\web\User',
            'identityClass' => 'app\models\Customer',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-app', 'httpOnly' => true],
        ],
        'blog' => [
            'class' => 'app\models\Blog',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'normalizer' => [
                'class' => 'yii\web\UrlNormalizer',
                'collapseSlashes' => true,
                'normalizeTrailingSlash' => true,
            ],
            'rules' => $rules,
        ],
        'formatter' => [
            'class' => 'app\components\Formatter',
        ],
        'view' => [
            'theme' => [
                'basePath' => '@app/views/' . $params['theme'],
                'pathMap' => [
                    '@app/views' => '@app/views/' . $params['theme'],
                ],
            ],
        ],
    ],
    'params' => $params['params'] + [
        'manifestIconSizes' => [16, 32, 60, 72, 76, 96, 114, 120, 144, 152, 180, 192],
        'manifestThemeColor' => "#3c8dbc",
    ],
];

if (YII_ENV == 'dev') {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        'allowedIPs' => ['127.0.0.1', '::1'],
    ];
}

$application = new yii\web\Application($config);
$application->on(yii\web\Application::EVENT_BEFORE_REQUEST, function (yii\base\Event $event) {
    $event->sender->response->on(yii\web\Response::EVENT_BEFORE_SEND, function ($e) {
        ob_start("ob_gzhandler");
    });
    $event->sender->response->on(yii\web\Response::EVENT_AFTER_SEND, function ($e) {
        ob_end_flush();
    });
});
$application->run();
