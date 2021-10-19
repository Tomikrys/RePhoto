<?php

namespace frontend\controllers;

use common\widgets\Alert;
use frontend\components\FrontendController;

/**
 * System controller
 */
class SystemController extends FrontendController
{
    /**
     * Displays actual alerts.
     */
    public function actionGetAllFlashMessages()
    {
        echo Alert::widget(['echoContent' => true]);
        exit();
    }
}
