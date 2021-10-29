<?php

namespace frontend\controllers;

use common\models\Photo;
use common\models\Place;
use frontend\components\FrontendController;
use frontend\models\UploadForm;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use yii\web\UploadedFile;

/**
 * Place controller
 */
class PlaceController extends FrontendController
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
                        'allow' => false,
                        'actions' => ['upload-photo', 'review-photo', 'align-photo', 'confirm-photo', 'create', 'update'],
                        'roles' => ['?'],
                    ],
                    [
                        'allow' => true,
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'view' => ['get'],
                ],
            ],
        ];
    }

    public function actionView(int $id)
    {
        $place = $this->findModel($id);
        $photos = $place->getPhotosStructure();
        $photosEdited = $place->getPhotosEditedStructure();

        return $this->render('view', [
            'place' => $place,
            'photos' => $photos,
            'photosEdited' => $photosEdited,
        ]);
    }

    public function findModel(int $id)
    {
        if ($model = Place::findOne($id)) {
            return $model;
        } else {
            throw new NotFoundHttpException('Place not exists');
        }
    }

    public function actionUploadPhoto(int $id_place)
    {
        $place = $this->findModel($id_place);
        $uploadForm = new UploadForm();

        if ($uploadForm->load(\Yii::$app->request->post())) {
            $uploadForm->file = UploadedFile::getInstance($uploadForm, 'file');
            if ($photoId = $uploadForm->saveFile($place->id)) {
                return $this->redirect(['review-photo', 'id_place' => $place->id, 'id_photo' => $photoId]);
            } else {
                \Yii::$app->session->setFlash('error', 'Error while uploading file.');
            }
        }

        $unpublishedPhotos = Photo::find()->andWhere(['id_place' => $id_place, 'aligned' => 0, 'id_user' => \Yii::$app->user->id])->all();

        return $this->render('add_photo/upload', [
            'place' => $place,
            'uploadForm' => $uploadForm,
            'unpublishedPhotos' => $unpublishedPhotos,
        ]);
    }

    public function actionReviewPhoto(int $id_photo, string $aligned = null)
    {
        $photoNew = Photo::findOne(['id' => $id_photo]);
        $place = $photoNew->place;
        if (!$photoNew) {
            throw new NotFoundHttpException('');
        }
        $photoOld = $place->oldestPhoto;

        $root = \Yii::getAlias('@root');

        $mainImgPath = $photoOld->getPath();
        $transformingImgPath = $photoNew->getPath();

        if ($aligned != null) {
            $transformedImagePath = $aligned;
        } else {
            $transformedImagePath = "/uploads/tmp/" . \Yii::$app->security->generateRandomString() . ".png";

            // manual align
            $points = \Yii::$app->request->post('points');
            if (!empty($points)) {
                foreach ($points['old'] as &$point) {
                    $point[0] = (int)$point[0];
                    $point[1] = (int)$point[1];
                }
                foreach ($points['new'] as &$point) {
                    $point[0] = (int)$point[0];
                    $point[1] = (int)$point[1];
                }
                $temp = tmpfile();
                fwrite($temp, json_encode($points));
                $tmpPath = stream_get_meta_data($temp)['uri'];
                // TODO define python path in config
                $command = "$root/python27/python.exe $root/homography-transformation-points.py $mainImgPath $transformingImgPath $root$transformedImagePath $tmpPath 2>&1";

            } else {
                // TODO define python path in config
                $command = "$root/python27/python.exe $root/homography-transformation.py $mainImgPath $transformingImgPath $root$transformedImagePath 2>&1";
            }

            $output = exec($command);
            $status = json_decode($output);

            if (isset($temp)) {
                fclose($temp);
            }

            if (!$status) {
                return $this->redirect(['/place/align-photo', 'id_photo' => $id_photo]);
            }

            $url = Url::to(['place/review-photo', 'id_photo' => $photoNew->id, 'aligned' => $transformedImagePath]);
            if (\Yii::$app->request->isPost) {
                return $url;
            } else {
                return $this->redirect($url);
            }
        }

        return $this->render('add_photo/review', [
            'place' => $place,
            'transformedImagePath' => $transformedImagePath,
            'photoOld' => $photoOld,
            'photoNew' => $photoNew,
        ]);
    }

    public function actionAlignPhoto(int $id_photo)
    {
        $photoNew = Photo::findOne(['id' => $id_photo]);
        $place = $photoNew->place;
        if (!$photoNew) {
            throw new NotFoundHttpException('');
        }
        $photoOld = $place->oldestPhoto;

        $root = \Yii::getAlias('@root');

        $mainImgPath = $photoOld->getPath();
        $transformingImgPath = $photoNew->getPath();

        // TODO define python path in config
        $command = escapeshellcmd("/Users/martinsikora/.virtualenvs/rephoto/bin/python $root/homography-points.py '$mainImgPath' '$transformingImgPath' 2>&1");

        $output = exec($command);
        $output = str_replace("'", '"', $output);
        $result = json_decode($output, true);

        $oldPoints = json_decode($result['old']);
        $newPoints = json_decode($result['new']);
        $data = [];
        $pc = count($oldPoints);
        for ($i = 0; $i < $pc; ++$i) {
            $data[] = [
                'old_point' => [
                    'x1' => 0,
                    'y1' => 0,
                    'x2' => $oldPoints[$i][0][0],
                    'y2' => $oldPoints[$i][0][1],
                ],
                'new_point' => [
                    'x1' => 0,
                    'y1' => 0,
                    'x2' => $newPoints[$i][0][0],
                    'y2' => $newPoints[$i][0][1],
                ],
                'color' => sprintf('#%06X', mt_rand(0, 0xFFFFFF)),
            ];
        }

        return $this->render('add_photo/align', [
            'place' => $place,
            'photoOld' => $place->oldestPhoto,
            'photoNew' => $photoNew,
            'points' => $data,
        ]);
    }

    public function actionConfirmPhoto(int $id_photo, string $aligned)
    {
        $photo = Photo::findOne(['id' => $id_photo]);
        $photo->aligned_photo = $aligned;
        $place = $photo->place;

        $post = \Yii::$app->request->post();
        if ($photo->load($post)) {
            $photo->aligned = true;
            $photo->visible = true;

            $trans = \Yii::$app->db->beginTransaction();

            if ($photo->save() && rename(\Yii::getAlias('@uploads/tmp/') . str_replace('/uploads/tmp/', '', $photo->aligned_photo), $photo->getPath())) {
                $trans->commit();
                \Yii::$app->session->setFlash('success', 'Photo successfully added.');
                return $this->redirect(['/place/view', 'id' => $place->id]);
            } else {
                $trans->rollBack();
                \Yii::$app->session->setFlash('error', 'Error while uploading file.');
            }
        }

        return $this->render('add_photo/confirm', [
            'place' => $place,
            'photo' => $photo,
        ]);
    }

    public function actionInfoWindow()
    {
        $lat = \Yii::$app->request->post('lat');
        $lng = \Yii::$app->request->post('lng');

        $elasticPlace = \frontend\models\elasticsearch\Place::find()->query([
            "bool" => [
                "filter" => [
                    "geo_distance" => [
                        "distance" => "0.5m",
                        "location" => [
                            "lat" => $lat,
                            "lon" => $lng,
                        ],
                    ],
                ],
            ]
        ])->one();

        if (!$elasticPlace) {
            throw new NotFoundHttpException();
        }

        $place = Place::findOne(['id' => $elasticPlace->id]);
        if (!$place) {
            throw new NotFoundHttpException();
        }

        $place = [
            'id' => $place->id,
            'name' => $place->name,
            'latitude' => $place->latitude,
            'longitude' => $place->longitude,
            'saved_by_logged_user' => $place->placeSavedForLoggedUser !== null,
            'oldest_photo' => [
                'id' => $place->oldestPhoto->id,
                'captured_at' => $place->oldestPhoto->captured_at,
                'thumbnail_url' => $place->oldestPhoto->getThumbnailUrl(),
            ],
            'newest_photo' => [
                'id' => $place->oldestPhoto->id,
                'captured_at' => $place->newestPhoto->captured_at,
                'thumbnail_url' => $place->newestPhoto->getThumbnailUrl(),
            ],
        ];

        return $this->render('info_window', [
            'place' => $place,
        ]);
    }

    public function actionCreate()
    {
        $place = new Place();
        $uploadForm = new UploadForm();

        if ($place->load(\Yii::$app->request->post())) {
            $place->id_user = \Yii::$app->user->id;

            $trans = \Yii::$app->db->beginTransaction();
            if ($place->save()) {
                $uploadForm->file = UploadedFile::getInstance($uploadForm, 'file');
                $id_photo = $uploadForm->saveFile($place->id, true);

                if ($id_photo) {
                    $trans->commit();
                    return $this->redirect(['view', 'id' => $place->id]);
                }
            }

            \Yii::$app->session->setFlash('error', 'Error while uploading file.');
            $trans->rollBack();
        }

        return $this->render('create', [
            'place' => $place,
            'uploadForm' => $uploadForm,
        ]);
    }

    public function actionUpdate(int $id_place)
    {
        $place = $this->findModel($id_place);

        if ($place->id_user !== \Yii::$app->user->id){
            throw new ForbiddenHttpException('This is not your place', 403);
        }

        if ($place->load(\Yii::$app->request->post())) {

            if ($place->save()) {
                \Yii::$app->session->setFlash('success', 'Place successfully changed.');
                return $this->redirect(Url::to(['view', 'id' => $place->id]));
            } else {
                \Yii::$app->session->setFlash('error', 'Error while uploading file.');
            }
        }

        return $this->render('update', [
            'place' => $place,
        ]);
    }

}
