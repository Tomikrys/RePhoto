<?php

namespace frontend\controllers;

use common\models\File;
use common\models\Photo;
use common\models\PhotoEdited;
use frontend\components\FrontendController;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\Response;
use yii\helpers\Url;

class TakephotoController extends FrontendController
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
                        //                        'roles' => ['@'],
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
     * Displays editor.
     *
     * @return mixed
     */
    public function actionIndex()
    {
        $photos = Photo::getPhotosForEditor();

        return $this->render('index', []);
    }
}
