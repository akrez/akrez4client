<?php

namespace app\controllers;

use app\components\Http;
use app\models\Blog;
use SimpleXMLElement;
use Yii;
use yii\web\Controller;
use yii\helpers\Url;
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
                    if (Yii::$app->user->isGuest) {
                        Yii::$app->user->setReturnUrl(Url::current());
                        return Yii::$app->controller->redirect(Blog::url('/site/signin'));
                    }
                    throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
                }
            ],
        ];
    }

    public function beforeAction($action)
    {
        if ($action->id == 'error') {
            $action->layout = 'blank';
            if (Yii::$app->params['blogName']) {
                Http::info();
                if (Blog::name()) {
                    $action->layout = 'main';
                }
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
        Http::search(['page_size' => -1]);

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

    public function actionIndex()
    {
        $this->view->params = Http::search(Yii::$app->request->get());
        return $this->render('index');
    }

    public function actionProduct($id)
    {
        $this->view->params = Http::product($id, Yii::$app->request->get());
        return $this->render('product');
    }

    public function actionCategory($id)
    {
        $this->view->params = Http::category($id, Yii::$app->request->get());
        return $this->render('category');
    }
}
