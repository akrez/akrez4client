<?php

namespace app\components;

use app\models\Customer;
use Yii;
use yii\base\Component;
use yii\httpclient\Client;
use yii\web\BadRequestHttpException;
use yii\web\ConflictHttpException;
use yii\web\ForbiddenHttpException;
use yii\web\GoneHttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\NotAcceptableHttpException;
use yii\web\NotFoundHttpException;
use yii\web\ServerErrorHttpException;
use yii\web\TooManyRequestsHttpException;
use yii\web\UnauthorizedHttpException;
use yii\web\UnsupportedMediaTypeHttpException;

class Http extends Component
{
    private static function post($url, $postData = [], $params = [])
    {
        $params = [
            '_token' => Customer::getIdentityToken(),
            '_blog'  => Yii::$app->params['blogName'],
        ] + $params;
        $fullUrl = Yii::$app->params['apiBaseUrl'] . $url . ($params ? '?' . http_build_query($params) : '');
        return (new Client(['transport' => 'yii\httpclient\CurlTransport']))
            ->createRequest()
            ->setHeaders(['X-Forwarded-For' => \Yii::$app->request->getUserIP()])
            ->setOptions(['userAgent' => \Yii::$app->request->userAgent])
            ->setMethod('POST')
            ->setUrl($fullUrl)
            ->setData($postData)
            ->send();
    }

    private static function postJson($url, $postData = [], $params = [], $routeByHttpCode = true, $setData = true)
    {
        $response = self::post($url, $postData, $params);
        $code = $response->getStatusCode();
        $data = $response->getData();

        if ($setData) {
            if (isset($data['_blog']) && $data['_blog']) {
                Yii::$app->blog->load($data, '_blog');
                Yii::$app->language = Yii::$app->blog->language;
            }
            if (isset($data['_constant_hash']) && $data['_constant_hash']) {
                $path = Yii::getAlias("@webroot") . "/constant/" . $data['_constant_hash'] . ".json";
                if (file_exists($path)) {
                    $constantData = json_decode(file_get_contents($path), true);
                } else {
                    $constantData = self::postJson('constant', [], [], false, false);
                    file_put_contents($path, json_encode($constantData));
                }
                Yii::$app->blog->setConstant($constantData);
            }
            Yii::$app->blog->setData($data);
        }

        if ($routeByHttpCode) {
            switch ($code) {
                case 200:
                    return $data;
                case 400:
                    throw new BadRequestHttpException;
                case 401:
                    throw new UnauthorizedHttpException;
                case 403:
                    throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
                case 404:
                    throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
                case 405:
                    throw new MethodNotAllowedHttpException;
                case 406:
                    throw new NotAcceptableHttpException;
                case 409:
                    throw new ConflictHttpException;
                case 410:
                    throw new GoneHttpException;
                case 415:
                    throw new UnsupportedMediaTypeHttpException;
                case 429:
                    throw new TooManyRequestsHttpException;
            }
            throw new ServerErrorHttpException('An internal server error occurred.');
        }

        return $data;
    }

    public static function postContent($url, $postData, $params)
    {
        $response = self::post($url, $postData, $params);
        if ($response->getStatusCode() == 200) {
            return $response->getContent();
        }
        return '';
    }

    public static function downloadImage($type, $path, $name)
    {
        $apiUrl = Yii::$app->params['apiBaseGalleryUrl'] . "image/$type/$name";
        $response = (new Client(['transport' => 'yii\httpclient\CurlTransport']))->createRequest()->setMethod('GET')->setUrl($apiUrl)->send();
        if ($response->statusCode == 200) {
            return (bool) file_put_contents($path, $response->getContent());
        }
        return false;
    }

    public static function exist()
    {
        return self::postJson('info', [], [], false);
    }

    public static function index($params)
    {
        return self::postJson('index', $params);
    }

    public static function page($entity, $pageType, $entityId)
    {
        return self::postContent('page', [], ['entity' => $entity, 'page_type' => $pageType, 'entity_id' => $entityId,]);
    }

    public static function category($id, $params)
    {
        return self::postJson('category', (array) $params, ['category_id' => $id]);
    }

    public static function product($id, $params)
    {
        return self::postJson('product', [], ['id' => $id]);
    }

    public static function info()
    {
        return self::postJson('info');
    }

    public static function login($user)
    {
        return self::postJson('login', [
            'mobile' => $user->mobile,
            'password' => $user->password,
        ]);
    }

    public static function signout()
    {
        return self::postJson('signout', [], [], false);
    }

    public static function verifyRequest($user)
    {
        return self::postJson('verify-request', [
            'mobile' => $user->mobile,
        ]);
    }

    public static function resetPasswordRequest($user)
    {
        return self::postJson('reset-password-request', [
            'mobile' => $user->mobile,
        ]);
    }

    public static function verify($user)
    {
        return self::postJson('verify', [
            'mobile' => $user->mobile,
            'verify_token' => $user->verify_token,
        ]);
    }

    public static function resetPassword($user)
    {
        return self::postJson('reset-password', [
            'mobile' => $user->mobile,
            'password' => $user->password,
            'reset_token' => $user->reset_token,
        ]);
    }

    public static function cart()
    {
        return self::postJson('cart');
    }

    public static function cartDelete($id)
    {
        return self::postJson('cart-delete', [], [
            'package_id' => $id,
        ]);
    }

    public static function cartAdd($package_id, $cnt, $add)
    {
        return self::postJson('cart-add', [], [
            'package_id' => $package_id,
            'cnt' => $cnt,
            'add' => $add,
        ]);
    }

    public static function invoices($params)
    {
        return self::postJson('invoices', $params);
    }

    public static function invoiceSubmit($invoice)
    {
        return self::postJson('invoice-submit', [
            'name' => $invoice->name,
            'phone' => $invoice->phone,
            'mobile' => $invoice->mobile,
            'city' => $invoice->city,
            'address' => $invoice->address,
            'des' => $invoice->des,
            'postal_code' => $invoice->postal_code,
            'lat' => $invoice->lat,
            'lng' => $invoice->lng,
        ]);
    }
}
