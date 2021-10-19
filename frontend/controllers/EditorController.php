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

/**
 * Editor controller
 */
class EditorController extends FrontendController
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

        return $this->render('index', [
            'photos' => $photos,
        ]);
    }

    public function actionHomography($id_main, $id_transform)
    {
        $main = Photo::findOne($id_main);
        $transform = Photo::findOne($id_transform);

        $root = \Yii::getAlias('@root');

        $mainImgPath = ltrim($main->getPath(), '/');
        $transformingImgPath = ltrim($transform->getPath(), '/');

        $transformedImagePath = "uploads/tmp/" . \Yii::$app->security->generateRandomString() . ".png";

        $command = escapeshellcmd("python $root/homography-transformation.py '$root/$mainImgPath' '$root/$transformingImgPath' '$root/$transformedImagePath'");
        $output = shell_exec($command);

        \Yii::$app->response->format = Response::FORMAT_JSON;
        return [
            'status' => json_decode($output),
            'url' => Url::to($transformedImagePath),
        ];
    }


    public function actionSavePicture(){
        \Yii::$app->response->format = Response::FORMAT_JSON;

        $data = \Yii::$app->request->post('data');
        if (!$data){
            return ['status' => false];
        }

        $editorArray = array_values(\Yii::$app->session->get('editor', []));

        $id_picture = \Yii::$app->request->post('id');
        if (!$id_picture || !($picture = PhotoEdited::findOne($id_picture))){
            $picture = new PhotoEdited();
        }

        $picture->setAttributes([
            'id_photo_1' => $editorArray[0],
            'id_photo_2' => $editorArray[1],
            'id_user' => \Yii::$app->user->id,
        ]);

        $file = $picture->file ?? new File();
        $file->id_user = $picture->id_user;
        $file->extension = 'png';

        $trans = \Yii::$app->db->beginTransaction();

        if ($file->save()){
            $picture->id_file = $file->id;
            if ($picture->save()){
                $data = str_replace('data:image/png;base64,', '', $data);
                $data = str_replace(' ', '+', $data);
                $fileData = base64_decode($data);

                if (file_put_contents($file->getPath(), $fileData) !== false){
                    $trans->commit();
                    \Yii::$app->session->setFlash('success', 'Photo successfully saved');
                    return ['status' => true, 'id' => $picture->id];
                }

            } else {
                $errors = $picture->errors;
            }
        } else {
            $errors = $file->errors;
        }

        \Yii::$app->session->setFlash('error', 'Error while saving photo');
        return ['status' => false, 'errors' => $errors];
    }

}
