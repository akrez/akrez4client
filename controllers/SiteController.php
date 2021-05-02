<?php

namespace app\controllers;

use app\components\Http;
use app\models\Blog;
use SimpleXMLElement;
use Yii;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\ForbiddenHttpException;
use yii\web\Response;

class SiteController extends Controller
{

    public function behaviors()
    {
        return [
            'access' => [
                'class' => 'yii\filters\AccessControl',
                'rules' => [
                    [
                        'actions' => ['error', 'index', 'category', 'product', 'sitemap', 'robots', 'manifest'],
                        'allow' => true,
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    /*
                    if (Yii::$app->user->isGuest) {
                        Yii::$app->user->setReturnUrl(Url::current());
                        return Yii::$app->controller->redirect(Blog::url('/site/signin'));
                    }
                    */
                    throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
                }
            ],
        ];
    }

    public function beforeAction($action)
    {
        if (Yii::$app->params['blogName'] && empty(Blog::name())) {
            Http::exist();
        }
        if (empty(Blog::name())) {
            if ($action instanceof ErrorAction) {
                $action->layout = 'blank';
            } else {
                $action->controller->layout = 'blank';
            }
        }
        return parent::beforeAction($action);
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    public function actionRobots()
    {
        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'text/plain; charset=UTF-8');

        return implode("\n", [
            'Sitemap: ' . Blog::url('site/sitemap', [], true),
            'User-agent: *',
            'Disallow: ',
        ]);
    }

    public function actionSitemap()
    {
        Http::index(['page_size' => -1]);

        Yii::$app->response->format = Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'application/xml; charset=UTF-8');
        $sitemap = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"></urlset>');

        $xmlurl = $sitemap->addChild('url');
        $xmlurl->addChild('loc', Blog::url('site/index', [], true));
        $xmlurl->addChild('priority', 1);
        foreach (Blog::categories() as $categoryId => $category) {
            $xmlurl = $sitemap->addChild('url');
            $xmlurl->addChild('loc', Blog::url('site/category', ['id' => $categoryId], true));
            $xmlurl->addChild('priority', 0.8);
        }
        foreach (Blog::getData('products') as $productId => $product) {
            $xmlurl = $sitemap->addChild('url');
            $xmlurl->addChild('loc', Blog::url('site/product', ['id' => $product['id']], true));
            $xmlurl->addChild('priority', 0.6);
        }

        return $sitemap->asXML();
    }

    public function actionManifest()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        //
        Http::info();
        //
        $icons = [];
        $logo = Blog::print('logo');
        foreach (Yii::$app->params['manifestIconSizes'] as $widthsValue) {
            $icons[] = [
                "src" => Blog::getImage('logo', $widthsValue . "_" . $widthsValue . "_100_1", $logo),
                "sizes" => $widthsValue . 'x' . $widthsValue,
            ];
        }
        //
        return [
            "name" => Blog::print('title'),
            "short_name" => ucfirst(Blog::name()),
            "display" => "standalone",
            "lang" => "fa",
            "dir" => "rtl",
            "start_url" => "/",
            "background_color" => "#FFFFFF",
            "theme_color" => Yii::$app->params['manifestThemeColor'],
            "orientation" => "portrait",
            "icons" => $icons,
        ];
    }

    public function actionIndex()
    {
        Http::index(Yii::$app->request->get());
        return $this->render('index');
    }

    public function actionProduct($id)
    {
        Http::product($id, Yii::$app->request->get());
        return $this->render('product');
    }

    public function actionCategory($id)
    {
        Http::category($id, Yii::$app->request->get());
        return $this->render('category');
    }
}
