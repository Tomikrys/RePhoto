<?php

namespace common\models;

use Yii;
use yii\db\ActiveRecord;

/**
 * This is the model class for table "place".
 *
 * @property integer $id
 * @property string $name
 * @property string $description
 * @property integer $latitude
 * @property integer $longitude
 * @property integer $id_user
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property Photo[] $photos
 * @property User $user
 */
class Place extends \yii\db\ActiveRecord
{
    const CAT_UNKNOWN = 0;
    const CAT_BUILDINGS = 1;
    const CAT_NATURE = 2;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'place';
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at', 'updated_at'],
                    ActiveRecord::EVENT_BEFORE_UPDATE => ['updated_at'],
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['name', 'id_user'], 'required'],
            [['description'], 'string'],
            [['id_user', 'created_at', 'updated_at', 'id_category'], 'integer'],
            [['latitude', 'longitude'], 'number'],
            [['name'], 'string', 'max' => 255],
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id_user' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/place', 'ID'),
            'name' => Yii::t('app/place', 'Name'),
            'description' => Yii::t('app/place', 'Description'),
            'latitude' => Yii::t('app/place', 'Latitude'),
            'longitude' => Yii::t('app/place', 'Longitude'),
            'id_user' => Yii::t('app/place', 'Created by'),
            'id_category' => Yii::t('app/place', 'Category'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),

            'gps' => Yii::t('app/place', 'GPS'),
        ];
    }

    public function getOldestPhoto()
    {
        return $this->hasOne(Photo::className(), ['id_place' => 'id'])
            ->andWhere(['visible' => 1])
            ->orderBy(['captured_at' => SORT_ASC]);
    }

    public function getNewestPhoto()
    {
        return $this->hasOne(Photo::className(), ['id_place' => 'id'])
            ->andWhere(['visible' => true])
            ->orderBy(['captured_at' => SORT_DESC]);
    }

    public function getPlaceSavedForLoggedUser()
    {
        return $this->hasOne(PlaceSaved::className(), ['id_place' => 'id'])->andWhere(['id_user' => Yii::$app->user->id]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'id_user']);
    }

    public function getPhotosStructure()
    {
        $photos = $this->getPhotos()
            ->with(['user', 'file'])
            ->andWhere(['visible' => true, 'aligned' => true])
            ->all();

        $result = [];
        foreach ($photos as $photo) {
            $result[] = [
                'id' => $photo->id,
                'in_editor' => $photo->getIsInEditorList(),
                'captured_at' => $photo->captured_at,
                'thumbnail_url' => $photo->getThumbnailUrl(),
                'original_url' => $photo->getUrl(),
                'user' => [
                    'id' => $photo->user->id,
                    'name' => $photo->user->name,
                ],
            ];
        }

        return $result;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhotos()
    {
        return $this->hasMany(Photo::className(), ['id_place' => 'id']);
    }

    public function getPhotosEditedStructure()
    {
        $photos = $this->getPhotosEdited()
            ->with(['user', 'file'])
            ->all();

        $result = [];
        foreach ($photos as $photo) {
            $result[] = [
                'thumbnail_url' => $photo->getThumbnailUrl(),
                'original_url' => $photo->getUrl(),
                'photo_1' => [
                    'captured_at' => $photo->photo1->captured_at,
                ],
                'photo_2' => [
                    'captured_at' => $photo->photo2->captured_at,
                ],
                'user' => [
                    'id' => $photo->user->id,
                    'name' => $photo->user->name,
                ],
            ];
        }

        return $result;
    }

    public function getPhotosEdited()
    {
        return PhotoEdited::find()
            ->with(['file', 'user'])
            ->joinWith([
                'photo1' => function ($query) {
                    $query->alias('photo_1');
                },
                'photo2' => function ($query) {
                    $query->alias('photo_2');
                },
            ])
            ->andWhere([
                'or',
                ['photo_1.id_place' => $this->id],
                ['photo_2.id_place' => $this->id],
            ]);
    }

    public function getCategoryName()
    {
        return $this::getCategories()[$this->id_category];
    }

    public static function getCategories()
    {
        return [
            static::CAT_BUILDINGS => Yii::t('app/place', 'Buildings'),
            static::CAT_NATURE => Yii::t('app/place', 'Nature'),
            static::CAT_UNKNOWN => Yii::t('app/place', 'Unknown'),
        ];
    }

    public function getUserPreview()
    {
        return $this->user->userPreview;
    }
}
