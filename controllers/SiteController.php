<?php

namespace app\controllers;

use app\components\Http;
use app\models\Blog;
use app\models\Customer;
use Yii;
use yii\web\Controller;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;

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
