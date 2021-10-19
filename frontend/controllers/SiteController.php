<?php

namespace frontend\controllers;

use frontend\components\FrontendController;
use frontend\models\search\PlaceSearch;

/**
 * Site controller
 */
class SiteController extends FrontendController
{
    /**
     * Displays homepage.
     * @return string
     */
    public function actionHomepage()
    {
        $searchModel = new PlaceSearch();
        $searchModel->map = false;
        $searchModel->na = true;
        $searchModel->bu = true;
        $searchModel->un = true;
        $places = $searchModel->getPlacesPreview(4);

        return $this->render('homepage', [
            'places' => $places,
        ]);
    }
}
