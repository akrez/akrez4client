<?php

$domains = require(__DIR__ . '/domains.php');
$serverName = $_SERVER['SERVER_NAME'];

$params = [
    'baseUrl' => '/akrez4client',
    'cookieValidationKey' => 'abcdefgh12345678abcdefgh12345678',
    'theme' => 'moderna',
    'db' => [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=localhost;dbname=akrez4client',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8',
        'schemaCacheDuration' => 3600,
        'schemaCache' => 'cache',
        'enableSchemaCache' => true,
    ],
    'params' => [
        'apiBaseUrl' => 'http://localhost/akrez4/api1/',
        'apiBaseGalleryUrl' => 'http://localhost/akrez3/site/gallery/',
    ],
];

if (isset($domains[$serverName])) {
    $params['params']['blogName'] = $domains[$serverName];
    $params['params']['isParked'] = true;
} else {
    $url = explode('.', $_SERVER['SERVER_NAME']);
    $params['params']['blogName'] = $url[0];
    $params['params']['isParked'] = false;
}

$domainsByBlogName = array_flip($domains);
if (!$params['params']['isParked'] && isset($domainsByBlogName[$params['params']['blogName']])) {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: http://" . $domainsByBlogName[$params['params']['blogName']], true, 301);
    header("Connection: close");
    exit();
}

return $params;
