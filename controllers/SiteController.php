<?php

namespace app\controllers;

use app\components\Http;
use app\models\Blog;
use app\models\Customer;
use app\models\Invoice;
use SimpleXMLElement;
use Yii;
use yii\helpers\Url;
use yii\web\Controller;
use yii\web\ErrorAction;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
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
                        'actions' => ['login', 'reset-password-request', 'reset-password', 'verify', 'verify-request'],
                        'allow' => true,
                        'verbs' => ['POST', 'GET'],
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['signout', 'cart', 'cart-add', 'cart-delete', 'invoices', 'invoice-view'],
                        'allow' => true,
                        'verbs' => ['POST', 'GET'],
                        'roles' => ['@'],
                    ],
                    [
                        'actions' => ['error', 'index', 'category', 'product', 'page', 'sitemap', 'robots', 'manifest', 'captcha'],
                        'allow' => true,
                        'verbs' => ['POST', 'GET'],
                        'roles' => ['?', '@'],
                    ],
                ],
                'denyCallback' => function ($rule, $action) {
                    if (Yii::$app->user->isGuest) {
                        Yii::$app->user->setReturnUrl(Url::current());
                        return Yii::$app->controller->redirect(Blog::url('/site/login'));
                    }
                    throw new ForbiddenHttpException(Yii::t('yii', 'You are not allowed to perform this action.'));
                }
            ],
        ];
    }

    public function actions()
    {
        return [
            'error' => [
                'class' => 'app\components\ErrorAction',
                'beforeAction' => function ($action) {
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
                }
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
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

    public function actionPage($id)
    {
        Http::info();
        if (!in_array($id, Blog::hasPage())) {
            throw new NotFoundHttpException(Yii::t('yii', 'Page not found.'));
        }
        return $this->render('page', [
            'id' => $id,
            'page' => Http::page('Blog', $id, Blog::name()),
        ]);
    }

    public function actionIndex()
    {
        Http::index(Yii::$app->request->get());
        $pages = Blog::hasPage();
        $hasPage = boolval(isset($pages['Index']) && $pages['Index']);
        return $this->render('index', [
            'page' => ($hasPage ? Http::page('Blog', 'Index', Blog::name()) : '')
        ]);
    }

    public function actionProduct($id)
    {
        Http::product($id, Yii::$app->request->get());
        $hasPage = boolval(Blog::getData('product', 'has_page', 'Index'));
        return $this->render('product', [
            'page' => ($hasPage ? Http::page('Product', 'Index', $id) : ''),
        ]);
    }

    public function actionCategory($id)
    {
        Http::category($id, Yii::$app->request->get());
        $hasPage = boolval(Blog::getData('category', 'has_page', 'Index'));
        return $this->render('category', [
            'page' => ($hasPage ? Http::page('Category', 'Index', $id) : ''),
        ]);
    }

    public function actionCartAdd($package_id, $cnt = 1, $add = true, $product_id = null)
    {
        $data = Http::cartAdd($package_id, $cnt, $add);
        if (empty($data['cart']['errors'])) {
            if ($add) {
                Yii::$app->session->setFlash('success', Yii::t('app', 'successfully added to cart'));
            } else {
                Yii::$app->session->setFlash('success', Yii::t('app', 'successfully cart edited'));
            }
            return $this->redirect(Blog::url('site/cart'));
        }
        $errors = [];
        foreach ($data['cart']['errors'] as $dataErrors) {
            foreach ($dataErrors as $error) {
                $errors[] = $error;
            }
        }
        Yii::$app->session->setFlash('error', $errors);
        if ($product_id) {
            return $this->redirect(Blog::url('site/product', ['id' => $product_id]));
        }
        return $this->redirect(Blog::url('site/cart'));
    }

    public function actionCartDelete($id)
    {
        $data = Http::cartDelete($id);
        if (isset($data['status']) && $data['status']) {
            Yii::$app->session->setFlash('success', Yii::t('app', 'successfully removed from cart'));
        }
        return $this->redirect(Blog::url('site/cart'));
    }

    public function actionInvoices()
    {
        Http::invoices(Yii::$app->request->get());
        return $this->render('invoices');
    }

    public function actionInvoiceView($id)
    {
        Http::invoiceView($id);
        return $this->render('invoice_view');
    }

    public function actionCart()
    {
        $invoice = new Invoice();
        if ($invoice->load(Yii::$app->request->post())) {
            $data = Http::invoiceSubmit($invoice);
            if ($invoice->load($data, 'invoice')) {
                if ($data['invoice']['errors']) {
                    $invoice->addErrors($data['invoice']['errors']);
                } else {
                    v('Hooo Hoooo');
                }
            }
        }
        Http::cart();
        return $this->render('cart', [
            'model' => $invoice,
        ]);
    }

    public function actionVerifyRequest()
    {
        $verifyRequest = new Customer(['scenario' => 'verifyRequest']);
        if (
            $verifyRequest->load(Yii::$app->request->post()) ||
            $verifyRequest->load(Yii::$app->request->get())
        ) {
            $data = Http::verifyRequest($verifyRequest);
            if ($verifyRequest->load($data, 'customer')) {
                if ($data['errors']) {
                    $verifyRequest->addErrors($data['errors']);
                } else {
                    Yii::$app->session->setFlash('success', Yii::t('app', 'alertVerifyRequestSuccessfull'));
                    return $this->redirect(Blog::url('site/verify', [
                        $verifyRequest->formName() . '[mobile]' => $verifyRequest->mobile,
                    ]));
                }
            }
        } else {
            $data = Http::info();
        }
        return $this->render('customer', [
            'model' => $verifyRequest,
        ]);
    }

    public function actionVerify()
    {
        $verify = new Customer(['scenario' => 'verify']);
        $verify->load(Yii::$app->request->get());
        if ($verify->load(Yii::$app->request->post())) {
            $data = Http::verify($verify);
            if ($verify->load($data, 'customer')) {
                if ($data['errors']) {
                    $verify->addErrors($data['errors']);
                } else {
                    Yii::$app->session->setFlash('success', Yii::t('app', 'alertVerifySuccessfull'));
                    return $this->redirect(Blog::url('site/index'));
                }
            }
        } else {
            $data = Http::info();
        }
        return $this->render('customer', [
            'model' => $verify,
        ]);
    }

    public function actionLogin()
    {
        $login = new Customer(['scenario' => 'login']);
        if ($login->load(Yii::$app->request->post())) {
            $data = Http::login($login);
            if ($login->load($data, 'customer')) {
                if ($data['customer']['token']) {
                    $user = Customer::login($data, $data['customer']['id'], $data['customer']['token']);
                    if (!$user->hasErrors()) {
                        Yii::$app->user->login($user);
                        Yii::$app->session->setFlash('success', Yii::t('app', 'alertLoginSuccessfull'));
                        return $this->goBack();
                    }
                }
                //
                if ($data['action'] == 'index') {
                    return $this->goBack();
                } elseif ($data['action'] == 'verify-request') {
                    return $this->redirect(Blog::url('site/verify-request', [
                        $login->formName() . '[mobile]' => $login->mobile,
                    ]));
                }
                if ($data['errors']) {
                    $login->addErrors($data['errors']);
                }
            }
        } else {
            $data = Http::info();
        }
        return $this->render('customer', [
            'model' => $login,
        ]);
    }

    public function actionSignout()
    {
        Http::signout();
        Yii::$app->user->logout();
        return $this->redirect(Blog::url('site/index'));
    }

    public function actionResetPasswordRequest()
    {
        $resetPasswordRequest = new Customer(['scenario' => 'resetPasswordRequest']);
        if (
            $resetPasswordRequest->load(Yii::$app->request->post()) ||
            $resetPasswordRequest->load(Yii::$app->request->get())
        ) {
            $data = Http::resetPasswordRequest($resetPasswordRequest);
            if ($resetPasswordRequest->load($data, 'customer')) {
                if ($data['errors']) {
                    $resetPasswordRequest->addErrors($data['errors']);
                } else {
                    Yii::$app->session->setFlash('success', Yii::t('app', 'alertResetPasswordRequestSuccessfull'));
                    return $this->redirect(Blog::url('site/reset-password', [
                        $resetPasswordRequest->formName() . '[mobile]' => $resetPasswordRequest->mobile,
                    ]));
                }
            }
        } else {
            $data = Http::info();
        }
        return $this->render('customer', [
            'model' => $resetPasswordRequest,
        ]);
    }

    public function actionResetPassword()
    {
        $resetPassword = new Customer(['scenario' => 'resetPassword']);
        $resetPassword->load(Yii::$app->request->get());
        if ($resetPassword->load(Yii::$app->request->post())) {
            $data = Http::resetPassword($resetPassword);
            if ($resetPassword->load($data, 'customer')) {
                if ($data['errors']) {
                    $resetPassword->addErrors($data['errors']);
                } else {
                    Yii::$app->session->setFlash('success', Yii::t('app', 'alertResetPasswordSuccessfull'));
                    return $this->redirect(Blog::url('site/index'));
                }
            }
        } else {
            $data = Http::info();
        }
        return $this->render('customer', [
            'model' => $resetPassword,
        ]);
    }
}
