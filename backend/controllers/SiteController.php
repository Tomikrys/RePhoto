<?php

namespace backend\controllers;

use backend\models\LoginForm;
use backend\models\search\PhotoSearch;
use common\models\Photo;
use common\models\Place;
use common\models\User;
use Yii;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

/**
 * Site controller
 */
class SiteController extends BackendController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return ArrayHelper::merge([
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['login', 'error'],
                        'allow' => true,
                    ],
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ], parent::behaviors());
    }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $previewData = [
            'photos' => Photo::find()->count(),
            'places' => Place::find()->count(),
            'users' => User::find()->andWhere(['role' => User::ROLE_USER])->count(),
            'photos_unverified' => Photo::find()->andWhere(['verified' => 0, 'aligned' => 1])->count(),
        ];

        $unverifiedPhotoSearchModel = new PhotoSearch(['unverifiedOnly' => true]);
        $unverifiedPhotoProvider = $unverifiedPhotoSearchModel->search(Yii::$app->request->queryParams);
        $unverifiedPhotoProvider->pagination->setPageSize(30);

        return $this->render('index', [
            'previewData' => $previewData,
            'unverifiedPhotoSearchModel' => $unverifiedPhotoSearchModel,
            'unverifiedPhotoProvider' => $unverifiedPhotoProvider,
        ]);
    }

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        } else {
            return $this->render('login', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Logout action.
     *
     * @return string
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }
}
