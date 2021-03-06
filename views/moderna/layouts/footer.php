<?php

/* @var $this \yii\web\View */
/* @var $content string */

use app\models\Blog;

$this->registerCss("
.footer-font-size {
    line-height: 1.62em !important;
    font-size: 16px;
}
.footer-font-size i {
    width: 28px;
    text-align: center;
}
");

?>
<footer class="footer pb-4 pt-2">
    <div class="container">
        <div class="row">
            <div class="col-sm-9 pt-2 text-justify">
                <h3 class="m-0 footer-font-size"><?= Blog::print('des') ?></h3>
                <?php
                if ($info = Blog::print('address')) {
                    echo '<p class="footer-font-size"><i class="fa  fa-map-marker-alt text-secondary"></i>' . $info . '</p>';
                }
                ?>
            </div>
            <div class="col-sm-3 pt-2 text-left footer-font-size" dir="ltr">
                <?php
                $parts = [];
                if ($info = Blog::print('email')) {
                    $parts[] = '<div><i class="fa fa-envelope text-warning"></i><a dir="ltr" href="' . Blog::getShareLink('email', $info) . '">' . $info . '</a></div>';
                }
                if ($info = Blog::print('phone')) {
                    $parts[] = '<div><i class="fa fa-phone text-info"></i><a dir="ltr" href="' . Blog::getShareLink('phone', $info) . '">' . $info . '</a></div>';
                }
                if ($info = Blog::print('twitter')) {
                    $parts[] = '<div><i class="fab fa-twitter text-primary"></i><a dir="ltr" href="' . Blog::getShareLink('twitter', $info) . '">' . $info . '</a></div>';
                }
                if ($info = Blog::print('telegram')) {
                    $parts[] = '<div><i class="fab fa-telegram text-info"></i><a dir="ltr" href="' . Blog::getShareLink('telegram', $info) . '">' . $info . '</a></div>';
                }
                if ($info = Blog::print('facebook')) {
                    $parts[] = '<div><i class="fab fa-facebook text-primary"></i><a dir="ltr" href="' . Blog::getShareLink('facebook', $info) . '">' . $info . '</a></div>';
                }
                if ($info = Blog::print('instagram')) {
                    $parts[] = '<div><i class="fab fa-instagram text-danger"></i><a dir="ltr" href="' . Blog::getShareLink('instagram', $info) . '">' . $info . '</a></div>';
                }
                if ($info = Blog::print('telegram_user')) {
                    $parts[] = '<div><i class="fab fa-telegram text-info"></i><a dir="ltr" href="' . Blog::getShareLink('telegram_user', $info) . '">' . $info . '</a></div>';
                }
                if ($info = Blog::print('whatsapp')) {
                    $parts[] = '<div><i class="fab fa-whatsapp text-success"></i><a dir="ltr" href="' . Blog::getShareLink('whatsapp', $info) . '">' . $info . '</a></div>';
                }
                echo implode('', $parts);
                ?>
            </div>
        </div>
    </div>
</footer>