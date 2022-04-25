<?php

namespace api\modules\v2\controllers;

use common\models\Photo;
use common\models\Place;
use common\models\User;
use frontend\models\UploadForm;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;
use yii\web\UploadedFile;

class PlacesController extends \api\components\ApiController
{
    public function actionIndex()
    {
        switch (\Yii::$app->request->method) {
            case 'POST':
                return $this->create();

            case 'GET':
                return $this->list();
            default:
                throw new MethodNotAllowedHttpException();
        }
    }

    public function create()
    {
        $auth = explode(' ', \Yii::$app->request->headers->get('authorization'));
        $token = $auth[1] ?? false;
        $user = User::findOne(['access_token' => $token]);
        if (!$user) {
            throw new HttpException(401, 'token is not valid');
        }
        \Yii::$app->user->setIdentity($user);

        $trans = \Yii::$app->db->beginTransaction();
        $place = new Place();
        $post = \Yii::$app->request->post();
        $place->setAttributes([
            'name' => $post['name'] ?? '',
            'description' => $post['description'] ?? '',
            'latitude' => $post['latitude'] ?? '',
            'longitude' => $post['longitude'] ?? '',
            'id_category' => $post['id_category'] ?? '',
            'id_user' => $user->id,
        ]);

        if (!$place->save()) {
            $trans->rollBack();
            throw new BadRequestHttpException(current($place->errors)[0]);
        }

        $uploadForm = new UploadForm();
        $uploadForm->file = UploadedFile::getInstanceByName('file');
        $id_photo = $uploadForm->saveFile($place->id, true);

        if (!$id_photo) {
            $trans->rollBack();
            throw new BadRequestHttpException('error while saving file');
        }

        $trans->commit();
        return [];
    }

    public function list()
    {
        $places = Place::find()
            ->select([
                'id', 'name', 'latitude', 'longitude',
            ])
            ->with([
                'photos' => function ($query) {
                    $query->select(['id', 'captured_at', 'id_file', 'id_place', 'id_user']);
                    $query->with([
                        'user' => function ($query) {
                            $query->select(['id', 'first_name', 'last_name']);
                        }
                    ])->asArray()->all();
                    $query->with([
                        'file' => function ($query) {
                            $query->select(['id', 'extension']);
                        }
                    ])->asArray()->all();
                }
            ])
            ->asArray()->all();

        return $places;
    }

    public function actionView(int $id)
    {
        switch (\Yii::$app->request->method) {
            case 'PUT':
                return $this->update($id);

            case 'GET':
                return $this->detail($id);
            case 'DELETE':
                return $this->delete($id);
            default:
                throw new MethodNotAllowedHttpException();
        }
    }

    public function update(int $id)
    {
        $auth = explode(' ', \Yii::$app->request->headers->get('authorization'));
        $token = $auth[1] ?? false;
        $user = User::findOne(['access_token' => $token]);
        if (!$user) {
            throw new HttpException(401, 'token is not valid');
        }
        \Yii::$app->user->setIdentity($user);

        $trans = \Yii::$app->db->beginTransaction();
        $place = Place::findOne(['id' => $id, 'id_user' => $user->id]);
        if (!$place) {
            throw new HttpException(400, 'place not exists or it is not yours');
        }
        $post = \Yii::$app->request->post();
        $place->setAttributes([
            'name' => $post['name'] ?? $place->name,
            'description' => $post['description'] ?? $place->description,
            'latitude' => $post['latitude'] ?? $place->latitude,
            'longitude' => $post['longitude'] ?? $place->longitude,
            'id_category' => $post['id_category'] ?? $place->id_category,
        ]);

        if (!$place->save()) {
            $trans->rollBack();
            throw new BadRequestHttpException(current($place->errors)[0]);
        }

        $trans->commit();
        return [];
    }

    public function detail(int $id)
    {
        $placeModel = Place::find()->andWhere(['id' => $id])->one();

        $place = $placeModel->attributes;
        $place['photos'] = $placeModel->getPhotosStructure();
        $place['photos_edited'] = $placeModel->getPhotosEditedStructure();

        return $place;
    }

    public function delete(int $id)
    {
        $auth = explode(' ', \Yii::$app->request->headers->get('authorization'));
        $token = $auth[1] ?? false;
        $user = User::findOne(['access_token' => $token]);
        if (!$user) {
            throw new HttpException(401, 'token is not valid');
        }
        \Yii::$app->user->setIdentity($user);

        $trans = \Yii::$app->db->beginTransaction();
        $place = Place::findOne(['id' => $id, 'id_user' => $user->id]);
        if (!$place) {
            throw new HttpException(400, 'place not exists or it is not yours');
        }
        if ($place->getPhotos()->andWhere(['!=', 'id_user', $user->id])->exists()) {
            throw new HttpException(400, 'place cannot be deleted, there are rephotos from other users');
        }

        if (Photo::deleteAll(['id_place' => $place->id]) == 0 || !$place->delete()) {
            $trans->rollBack();
            throw new BadRequestHttpException(current($place->errors)[0]);
        }

        $trans->commit();
        return [];
    }

    public function actionPhoto(int $id, int $id_photo = null)
    {
        switch (\Yii::$app->request->method) {
            //case 'PUT':
              //  return $this->updatePhoto($id, $id_photo);
            case 'POST':
                return $this->addPhoto($id);
            case 'DELETE':
                return $this->deletePhoto($id, $id_photo);
            default:
                throw new MethodNotAllowedHttpException();
        }
    }

    public function addPhoto(int $id)
    {
        $auth = explode(' ', \Yii::$app->request->headers->get('authorization'));
        $token = $auth[1] ?? false;
        $user = User::findOne(['access_token' => $token]);
        if (!$user) {
            throw new HttpException(401, 'token is not valid');
        }
        \Yii::$app->user->setIdentity($user);

        $trans = \Yii::$app->db->beginTransaction();
        $place = Place::findOne(['id' => $id, 'id_user' => $user->id]);
        if (!$place) {
            throw new HttpException(400, 'place not exists or it is not yours');
        }

        $uploadForm = new UploadForm();
        $uploadForm->file = UploadedFile::getInstanceByName('file');
        $id_photo = $uploadForm->saveFile($place->id, false);

        if (!$id_photo) {
            $trans->rollBack();
            throw new BadRequestHttpException('error while saving file');
        }

        $trans->commit();
        return [];
    }


    public function deletePhoto(int $id, int $id_photo)
    {
        $auth = explode(' ', \Yii::$app->request->headers->get('authorization'));
        $token = $auth[1] ?? false;
        $user = User::findOne(['access_token' => $token]);
        if (!$user) {
            throw new HttpException(401, 'token is not valid');
        }
        \Yii::$app->user->setIdentity($user);

        $trans = \Yii::$app->db->beginTransaction();
        $photo = Photo::findOne(['id' => $id_photo, 'id_user' => $user->id, 'id_place' => $id]);
        if (!$photo) {
            throw new HttpException(400, 'photo not exists or it is not yours');
        }

        if (!$photo->delete()) {
            $trans->rollBack();
            throw new BadRequestHttpException('error while deleting file');
        }

        $trans->commit();
        return [];
    }
}