<?php

namespace frontend\controllers;

use common\models\Photo;
use common\models\PhotoWishList;
use frontend\components\FrontendController;
use frontend\models\search\PhotoSearch;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Photo controller
 */
class PhotoController extends FrontendController
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
                        'actions' => ['delete' => ['POST'], 'detail', 'add-to-editor-list', 'remove-from-editor-list'],
                    ],
                    [
                        'allow' => true,
                        'roles' => ['@'],
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
     * Displays photo's detail.
     * @param $id photo identification
     * @return bool|string false on error, otherwise html content
     */
    public function actionDetail($id)
    {
        $photo = Photo::findOne($id);

        if (\Yii::$app->request->isAjax) {
            return !$photo ? false : $this->render('partial/detail', [
                'photo' => $photo,
            ]);
        } else {
            if (!$photo) {
                throw new NotFoundHttpException('Fotografie neexistuje');
            }

            return $this->render('@frontend/views/photo/detail', [
                'photo' => $photo,
            ]);
        }
    }

    /**
     * Displays form to upload new photos.
     * @return mixed
     */
    public function actionAdd()
    {
        return $this->render('add');
    }

    /**
     * Saves uploaded files.
     * @return bool
     */
    public function actionUpload()
    {
        $fileName = 'file';

        # check if there is uploaded file
        if (isset($_FILES[$fileName])) {
            // get file
            $file = UploadedFile::getInstanceByName($fileName);

            # create new record for photo in database
            $photo = new Photo();
            $photo->setAttributesFromFile($file);
            $photo->setExifDataFromFile($file);
            // TODO unaligned, unvisible
            // $photo->aligned = 0;
            // $photo->visible = 0;

            $trans = \Yii::$app->db->beginTransaction();
            if ($photo->save()) {
                # save file to uploads folder
                if ($file->saveAs(\Yii::getAlias('@uploads') . '/' . $photo->id)) {

                    $photo->createThumbnail();

                    $trans->commit();
                    return true;
                }
            }

            $trans->rollBack();
        }

        return false;
    }

    /**
     * Edits photo. Only user who uploaded specific photo can edit it.
     * @param $id photo identification
     * @return string html content
     * @throws ForbiddenHttpException user does not upload this photo
     * @throws NotFoundHttpException photo does not exists
     */
    public function actionEdit($id)
    {
        $photo = Photo::findOne($id);

        # check if photo exists
        if (!$photo) {
            throw new NotFoundHttpException('Fotografie neexistuje.');
        }

        # check if logged user uploaded this photo
        $loggedUser = \Yii::$app->user;
        if ($loggedUser->id !== $photo->id_user) {
            throw new ForbiddenHttpException('Nemáte oprávnění editovat tuto fotografii.');
        }

        $photo->setScenario('update');

        $post = \Yii::$app->request->post('Photo');
        if (!empty($post)) {
            $photo->setAttributes($post);
            if ($photo->save()) {
                // refresh map
                $this->view->registerJs('refreshMapAndSideNav();');
                return $this->actionUnpublished();
            }
        }

        return $this->render('edit', [
            'photo' => $photo,
        ]);
    }

    /**
     * Deletes an existing Photo model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $photo = Photo::findOne($id);
        $loggedUser = \Yii::$app->user;
        if ($loggedUser->id !== $photo->id_user) {
            throw new ForbiddenHttpException('Nemáte oprávnění editovat tuto fotografii.');
        }
        $photo->delete();
        \frontend\models\elasticsearch\Place::refreshData();

        return $this->redirect(\Yii::$app->request->referrer ?: \Yii::$app->homeUrl);
    }

    /**
     * Displays unpublished photos for logged user.
     * @return string html content
     */
    public function actionUnpublished()
    {
        $searchModel = new PhotoSearch();
        $dataProvider = $searchModel->searchUnpublished(\Yii::$app->request->queryParams);

        return $this->render('unpublished', [
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays unaligned photos for logged user.
     * @return string html content
     */
    public function actionUnaligned()
    {
        $searchModel = new PhotoSearch();
        $dataProvider = $searchModel->searchUnaligned(\Yii::$app->request->queryParams);

        return $this->render('unaligned', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionFavorite()
    {
        $searchModel = new PhotoSearch();
        $dataProvider = $searchModel->searchFavorite(\Yii::$app->user->id);

        return $this->render('favorite', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Adds photo to logged user's wish list.
     */
    public function actionAddToWishList()
    {
        $photoId = (int)\Yii::$app->request->post('id');
        $userId = \Yii::$app->user->id;

        if (PhotoWishList::create($photoId, $userId)) {
            \Yii::$app->session->setFlash('success', 'Fotka přidána mezi oblíbené');
        } else {
            \Yii::$app->session->setFlash('error', 'Chyba při přidání fotky mezi oblíbené');
        }
    }

    /**
     * Removes photo from logged user's wish list.
     */
    public function actionRemoveFromWishList()
    {
        $photoId = (int)\Yii::$app->request->post('id');
        $userId = \Yii::$app->user->id;

        if (PhotoWishList::remove($photoId, $userId)) {
            \Yii::$app->session->setFlash('success', 'Fotka odebrána z oblíbených');
        } else {
            \Yii::$app->session->setFlash('error', 'Chyba při mazání fotky z oblíbených');
        }
    }

    /**
     * Adds photo to editor list.
     */
    public function actionAddToEditorList()
    {
        $photoId = (int)\Yii::$app->request->post('id');
        $userId = \Yii::$app->user->id ?? null;

        if (Photo::addToEditor($photoId, $userId)) {
            \Yii::$app->session->setFlash('success', 'Fotka přidána do seznamu pro editor');
        } else {
            \Yii::$app->session->setFlash('error', 'Chyba při přidání fotky do seznamu pro editor');
        }
    }

    /**
     * Removes photo from editor list.
     */
    public function actionRemoveFromEditorList()
    {
        $photoId = (int)\Yii::$app->request->post('id');

        if (Photo::removeFromEditor($photoId)) {
            \Yii::$app->session->setFlash('success', 'Fotka odebrána ze seznamu pro editor');
        } else {
            \Yii::$app->session->setFlash('error', 'Chyba při mazání fotky ze seznamu pro editor');
        }
    }
}
