<?php

/* @var $this \yii\web\View */
/* @var $content string */

use yii\helpers\Html;
use app\models\Blog;

?>
<footer class="footer pb-4 pt-2">
    <div class="container">
        <div class="row ">
            <div class="col-sm-3 pt-2 text-center">
                <h3 class="mt-0"><?= Blog::print('title') ?></h3>
                <h5 class="mt-0"><?= Blog::print('slug') ?></h5>
            </div>
            <div class="col-sm-7 pt-2 text-center">
                <div class="row">
                    <div class="col-sm-12">
                        <?php
                        $parts = [];
                        if (Blog::print('address')) {
                            $parts[] = '<p>' . (Blog::print('address')) . '</p>';
                        }
                        $tels = [];
                        if (Blog::print('email')) {
                            $email = (Blog::print('email'));
                            $tels[] = '<a href="mailto:' . $email . '">' . $email . '</a>';
                        }
                        if (Blog::print('phone')) {
                            $phone = (Blog::print('phone'));
                            $tels[] = '<a dir="ltr" href="tel:' . $phone . '">' . $phone . '</a>';
                        }
                        if (Blog::print('mobile')) {
                            $mobile = (Blog::print('mobile'));
                            $tels[] = '<a dir="ltr" href="tel:' . $mobile . '">' . $mobile . '</a>';
                        }
                        if ($tels) {
                            $parts[] = implode(' | ', $tels);
                        }
                        echo implode('', $parts);
                        ?>
                    </div>
                </div>
            </div>
            <div class="col-sm-2 pt-2">
                <div class="row">
                    <?php
                    if (Blog::print('facebook')) {
                        $url = ('https://www.facebook.com/' . Blog::print('facebook'));
                        $logo = Html::img(Yii::getAlias('@web/cdn/image/social/facebook.svg'), ['style' => 'margin: auto;', 'class' => 'img-fluid rounded', 'alt' => $url]);
                        echo '<div class="col-sm-4 col-3 mb-1">' . Html::a($logo, $url, ['style' => 'text-align: center;']) . '</div>';
                    }
                    ?>
                    <?php
                    if (Blog::print('twitter')) {
                        $url = ('https://twitter.com/' . Blog::print('twitter'));
                        $logo = Html::img(Yii::getAlias('@web/cdn/image/social/twitter.svg'), ['style' => 'margin: auto;', 'class' => 'img-fluid rounded', 'alt' => $url]);
                        echo '<div class="col-sm-4 col-3 mb-1">' . Html::a($logo, $url, ['style' => 'text-align: center;']) . '</div>';
                    }
                    ?>
                    <?php
                    if (Blog::print('telegram')) {
                        $url = ('https://telegram.me/' . Blog::print('telegram'));
                        $logo = Html::img(Yii::getAlias('@web/cdn/image/social/telegram.svg'), ['style' => 'margin: auto;', 'class' => 'img-fluid rounded', 'alt' => $url]);
                        echo '<div class="col-sm-4 col-3 mb-1">' . Html::a($logo, $url, ['style' => 'text-align: center;']) . '</div>';
                    }
                    ?>
                    <?php
                    if (Blog::print('instagram')) {
                        $url = ('https://www.instagram.com/' . Blog::print('instagram'));
                        $logo = Html::img(Yii::getAlias('@web/cdn/image/social/instagram.svg'), ['style' => 'margin: auto;', 'class' => 'img-fluid rounded', 'alt' => $url]);
                        echo '<div class="col-sm-4 col-3 mb-1">' . Html::a($logo, $url, ['style' => 'text-align: center;']) . '</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>
</footer>