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
    private static function postRequest($url, $postData = [], $params = [])
    {
        $params = [
            '_token' => Customer::getIdentityToken(),
            '_blog'  => Yii::$app->params['blogName'],
        ] + $params;
        $fullUrl = Yii::$app->params['apiBaseUrl'] . $url . ($params ? '?' . http_build_query($params) : '');
        $data = (new Client(['transport' => 'yii\httpclient\CurlTransport']))
            ->createRequest()
            ->setHeaders(['X-Forwarded-For' => \Yii::$app->request->getUserIP()])
            ->setOptions(['userAgent' => \Yii::$app->request->userAgent])
            ->setMethod('POST')
            ->setUrl($fullUrl)
            ->setData($postData)
            ->send()
            ->getData();
        return $data;
    }

    private static function post($url, $postData = [], $params = [])
    {
        $data = self::postRequest($url, $postData, $params);
        if (isset($data['_blog']) && $data['_blog']) {
            Yii::$app->blog->load($data, '_blog');
        }
        if (isset($data['_constant_hash']) && $data['_constant_hash']) {
            Yii::$app->blog->setConstant(self::constant($data['_constant_hash']));
        }
        if (isset($data['_categories']) && $data['_categories']) {
            Yii::$app->blog->categories = $data['_categories'];
        }
        switch ($data['_code']) {
            case 200:
                return $data;
            case 400:
                throw new BadRequestHttpException;
            case 401:
                throw new UnauthorizedHttpException;
            case 403:
                throw new ForbiddenHttpException('You are not allowed to perform this action.');
            case 404:
                throw new NotFoundHttpException('Page not found.');
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

    public static function gallery($type, $whq, $name)
    {
        $type = preg_replace('/[^A-Za-z0-9\-\.\_]/', '', $type);
        $whq = preg_replace('/[^A-Za-z0-9\-\.\_]/', '', $whq);
        $name = preg_replace('/[^A-Za-z0-9\-\.\_]/', '', $name);
        //
        $basePath = Yii::getAlias("@webroot/gallery/$type/$whq");
        $path = "$basePath/$name";
        $apiUrl = Yii::$app->params['apiBaseGalleryUrl'] . "$type/$whq/$name";
        $url = Yii::getAlias("@web") . "/gallery/$type/$whq/$name";
        //
        if (file_exists($path)) {
            return $url;
        }
        //
        $response = (new Client(['transport' => 'yii\httpclient\CurlTransport']))->createRequest()->setMethod('GET')->setUrl($apiUrl)->send();
        if ($response->statusCode == 200) {
            file_exists($basePath) || mkdir($basePath, '755', true);
            file_put_contents($path, $response->getContent());
        }
        return $url;
    }

    public static function constant($constantId)
    {
        $path = Yii::getAlias("@webroot") . "/cdn/constant/$constantId.json";
        if (file_exists($path)) {
            return json_decode(file_get_contents($path), true);
        }
        $data = self::postRequest('constant', [], [], false);
        file_put_contents($path, json_encode($data));
        return $data;
    }

    public static function exist()
    {
        return self::postRequest('info');
    }

    public static function search($params)
    {
        return self::post('search', $params);
    }

    public static function category($id, $params)
    {
        return self::post('search', (array) $params, ['categoryId' => $id]);
    }

    public static function product($id, $params)
    {
        return self::post('product', [], ['id' => $id]);
    }

    public static function info()
    {
        return self::post('info');
    }

    public static function signin($user)
    {
        return self::post('signin', [
            'email' => $user->email,
            'password' => $user->password,
        ]);
    }

    public static function signup($user)
    {
        return self::post('signup', [
            'email' => $user->email,
            'password' => $user->password,
        ]);
    }

    public static function signout()
    {
        return self::post('signout');
    }

    public static function resetPasswordRequest($user)
    {
        return self::post('reset-password-request', [
            'email' => $user->email,
        ]);
    }

    public static function resetPassword($user)
    {
        return self::post('reset-password', [
            'email' => $user->email,
            'password' => $user->password,
            'reset_token' => $user->reset_token,
        ]);
    }

    public static function basket()
    {
        return self::post('basket');
    }

    public static function basketRemove($id)
    {
        return self::post('basket-remove', [], [
            'package_id' => $id,
        ]);
    }

    public static function basketAdd($id, $cnt)
    {
        return self::post('basket-add', ['cnt' => $cnt], ['package_id' => $id,]);
    }

    public static function invoiceAdd($invoice)
    {
        return self::post('invoice-add', [
            'Invoice' => [
                'name' => $invoice->name,
                'phone' => $invoice->phone,
                'mobile' => $invoice->mobile,
                'province' => $invoice->province,
                'address' => $invoice->address,
                'des' => $invoice->des,
            ]
        ]);
    }

    public static function invoice($params)
    {
        return self::post('invoice', $params);
    }

    public static function invoiceRemove($id)
    {
        return self::post('invoice-remove', [], [
            'id' => $id,
        ]);
    }

    public static function invoiceView($id)
    {
        return self::post('invoice-view', [], [
            'id' => $id,
        ]);
    }
}
