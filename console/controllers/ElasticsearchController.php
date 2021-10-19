<?php

namespace console\controllers;

use frontend\models\elasticsearch\Place;
use yii\console\Controller;

class ElasticsearchController extends Controller
{
    public function actionInit()
    {
        Place::refreshData();
    }
}