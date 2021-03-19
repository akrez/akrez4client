<?php

namespace app\controllers;

use app\components\Http;
use app\models\Blog;
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
                        'actions' => ['signin', 'signup', 'reset-password-request', 'reset-password'],
                        'allow' => true,
                        'verbs' => ['GET', 'POST'],
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['error', 'index', 'category', 'product', 'sitemap', 'robots', 'manifest'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['signout', 'basket', 'basket-remove', 'basket-add', 'invoice', 'invoice-view', 'invoice-remove',],
                        'allow' => true,
                        'verbs' => ['GET', 'POST'],
                        'roles' => ['@'],
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
                Http::exist();
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
        Http::exist();
        return $this->render('index');
    }

    public function actionSignout()
    {
        Http::signout();
        Yii::$app->user->logout();
        return $this->redirect(Blog::firstPageUrl());
    }
}
