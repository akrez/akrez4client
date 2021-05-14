<?php

namespace app\components;

use Closure;
use Yii;
use yii\web\ErrorAction as WebErrorAction;

class ErrorAction extends WebErrorAction
{
    public $beforeAction;

    public function run()
    {
        if ($this->beforeAction instanceof Closure) {
            call_user_func($this->beforeAction, $this);
        }
        return parent::run();
    }
}
