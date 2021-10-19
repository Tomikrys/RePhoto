<?php

namespace frontend\controllers;

use common\models\Place;
use frontend\components\FrontendController;
use frontend\models\search\PlaceSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\Response;

/**
 * Map controller
 */
class MapController extends FrontendController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'index' => ['get'],
                ],
            ],
        ];
    }

    /**
     * Displays map with photos.
     *
     * @param float|null $lat map center point latitude
     * @param float|null $lng map center point longitude
     * @param int|null $zoom map zoom level
     * @return string html content
     */
    public function actionIndex(float $lat = 49.957978, float $lng = 15.174409, int $zoom = 7, $ymin = null, $ymax = null, $bu = 1, $na = 1, $un = 1, $map = 1)
    {
        $sliderMin = \common\models\Photo::find()->min('captured_at');
        $placeFilter = new PlaceSearch();
        $placeFilter->setAttributes([
            'ymin' => $ymin ?? ($sliderMin ? (new \DateTime($sliderMin))->format('Y') : date('Y')),
            'ymax' => $ymax ?? date('Y'),
            'bu' => $bu,
            'na' => $na,
            'un' => $un,
            'map' => $map,
        ]);
        $categories = Place::getCategories();

        return $this->render('index', [
            'placeFilter' => $placeFilter,
            'categories' => $categories,
            'mapSettings' => [
                'lat' => $lat,
                'lng' => $lng,
                'zoom' => $zoom,
            ]
        ]);
    }

    public function actionPlaces()
    {
        $searchModel = new PlaceSearch();
        $data = $searchModel->getMapData(\Yii::$app->request->post());

        $placesPreview = $this->renderAjax('partial/places', ['places' => $data['places'], 'page' => $data['page'], 'lastFetch' => $data['lastFetch']]);

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'markers' => $data['markers'],
//            'places' => $data['places'],
            'page' => $data['page'],
            'lastFetch' => $data['lastFetch'],
            'placesPreview' => $placesPreview,
        ];
    }
}
