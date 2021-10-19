<?php

namespace common\models;

use Yii;
use yii\base\ErrorException;
use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * This is the model class for table "photo".
 *
 * @property integer $id
 * @property string $captured_at
 * @property string $name
 * @property string $description
 * @property string $latitude
 * @property string $longitude
 * @property integer $aligned
 * @property integer $id_user
 * @property integer $id_file
 * @property integer $id_place
 * @property integer $visible
 * @property string $exif_json
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property File $file
 * @property Place $place
 * @property User $user
 * @property PhotoComment[] $photoComments
 * @property PhotoLike[] $photoLikes
 * @property PhotoSave[] $photoSaves
 * @property PhotoTag[] $photoTags
 * @property Rephoto[] $rephotos
 */
class Photo extends \yii\db\ActiveRecord
{
    public $aligned_photo;

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'photo';
    }

    public static function addToEditor(int $id_photo, int $id_user = null)
    {
        #check if photo exists
        $photo = static::findOne($id_photo);
        if (!$photo) {
            return false;
        }

        # check if photo is not visible and logged user is not an owner
//        if ($photo->visible === false && $photo->id_user !== $id_user) {
        if ($photo->visible === false) {
            return false;
        }

        # add to session
        $sessionArray = Yii::$app->session->get('editor', []);
        if (!in_array($id_photo, $sessionArray)) {
            $sessionArray[] = $id_photo;
            Yii::$app->session->set('editor', $sessionArray);
        }

        return true;
    }

    public static function removeFromEditor(int $id_photo)
    {
        # add to session
        $sessionArray = Yii::$app->session->get('editor', []);
        $key = array_search($id_photo, $sessionArray);
        if ($key !== false) {
            unset($sessionArray[$key]);
            Yii::$app->session->set('editor', $sessionArray);

        }

        return true;
    }

    public static function getPhotosForEditor()
    {
        $ids = Yii::$app->session->get('editor', []);
        if (empty($ids)) {
            return [];
        }

        return Photo::find()->andWhere(['id' => $ids])->all();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_user'], 'required', 'on' => ['upload']],
            [['name', 'id_user'], 'required', 'on' => ['update']],

            [['description', 'exif_json'], 'string'],
            [['id_user', 'created_at', 'updated_at', 'visible', 'aligned'], 'integer'],
            [['name'], 'string', 'max' => 255],

            ['aligned', 'default', 'value' => 0],

            ['captured_at', 'string'],

            [['latitude'], 'number', 'max' => 90, 'min' => -90],
            [['longitude'], 'number', 'max' => 180, 'min' => -180],

            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id_user' => 'id']],
            [['id_file'], 'exist', 'skipOnError' => true, 'targetClass' => File::className(), 'targetAttribute' => ['id_file' => 'id']],
            [['id_place'], 'exist', 'skipOnError' => true, 'targetClass' => Place::className(), 'targetAttribute' => ['id_place' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/photo', 'ID'),
            'name' => Yii::t('app/photo', 'Name'),
            'description' => Yii::t('app/photo', 'Description'),
            'latitude' => Yii::t('app/photo', 'Latitude'),
            'longitude' => Yii::t('app/photo', 'Longitude'),
            'id_user' => Yii::t('app/photo', 'Created by'),
            'created_at' => Yii::t('app', 'Created at'),
            'updated_at' => Yii::t('app', 'Updated at'),
            'visible' => Yii::t('app/photo', 'Visible'),
            'captured_at' => Yii::t('app/photo', 'Captured at'),
            'id_file' => Yii::t('app/photo', 'Photo'),
            'aligned' => Yii::t('app/photo', 'Aligned'),
            'verified' => Yii::t('app/photo', 'Verified'),
        ];
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
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'id_user']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhotoWishLists()
    {
        return $this->hasMany(PhotoWishList::className(), ['id_photo' => 'id']);
    }

    public function getLoggedUserWishList()
    {
        return $this->hasOne(PhotoWishList::className(), ['id_photo' => 'id'])->andWhere(['id_user' => Yii::$app->user->id]);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFile()
    {
        return $this->hasOne(File::className(), ['id' => 'id_file']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPlace()
    {
        return $this->hasOne(Place::className(), ['id' => 'id_place']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhotoComments()
    {
        return $this->hasMany(PhotoComment::className(), ['id_photo' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhotoLikes()
    {
        return $this->hasMany(PhotoLike::className(), ['id_photo' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhotoSaves()
    {
        return $this->hasMany(PhotoSave::className(), ['id_photo' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhotoTags()
    {
        return $this->hasMany(PhotoTag::className(), ['id_photo' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRephotos()
    {
        // TODO id_photo_2
        return $this->hasMany(Rephoto::className(), ['id_photo_1' => 'id']);
    }

    public function setAttributesFromFile($file)
    {
        $this->id_user = Yii::$app->user->id;
        $this->name = $file->name;
    }

    public function getPath()
    {
        return Yii::getAlias('@uploads/' . $this->id . '.' . $this->file->extension);
    }

    public function getThumbnailUrl()
    {
        return Url::to('/uploads/' . $this->id_file . '.' . $this->file->extension);
    }

    public function getUrl()
    {
        return Url::to('/uploads/' . $this->id_file . '.' . $this->file->extension);
    }

    public function setExifDataFromFile($file)
    {
        // TODO captured at
        $this->captured_at = date('Y-m-d H:i:s');

        try {
            $data = exif_read_data($file->tempName, null, true);
        } catch (ErrorException $e) {
            // unsupported file type
            return false;
        }

        $this->exif_json = json_encode($data);

        function getComputedAttribute($attr)
        {
            $exploded = explode('/', $attr);

            return $exploded[0] / $exploded[1];
        }

        // parse GPS
        if ($gps = $data['GPS'] ?? false) {
            if (isset($gps['GPSLatitude']) && isset($gps['GPSLongitude'])) {
                // parse latitude
                $this->latitude = getComputedAttribute($gps['GPSLatitude'][0])
                    + (getComputedAttribute($gps['GPSLatitude'][1]) / 60.0)
                    + (getComputedAttribute($gps['GPSLatitude'][2]) / 3600.0);
                $this->latitude *= ($gps['GPSLatitudeRef'] == 'N' ? 1 : -1);

                // parse longitude
                $this->longitude = getComputedAttribute($gps['GPSLongitude'][0])
                    + (getComputedAttribute($gps['GPSLongitude'][1]) / 60.0)
                    + (getComputedAttribute($gps['GPSLongitude'][2]) / 3600.0);
                $this->longitude *= ($gps['GPSLongitudeRef'] == 'E' ? 1 : -1);
            }
        }

        return true;
    }

    public function createThumbnail()
    {
        return $this::createScaledImage(Yii::getAlias('@uploads/') . $this->id_file . '.' . $this->file->extension, Yii::getAlias('@uploads/') . $this->id_file . '-32', 32, 32);
    }

    public static function createScaledImage($src, $dest, $desired_width = false, $desired_height = false)
    {
        /*If no dimenstion for thumbnail given, return false */
        if (!$desired_height && !$desired_width) return false;

        $type = exif_imagetype($src);

        /* read the source image */
        if ($type === IMAGETYPE_GIF)
            $resource = imagecreatefromgif($src);
        else if ($type === IMAGETYPE_PNG)
            $resource = imagecreatefrompng($src);
        else if ($type === IMAGETYPE_JPEG)
            $resource = imagecreatefromjpeg($src);
        else
            return false;

        $width = imagesx($resource);
        $height = imagesy($resource);
        /* find the "desired height" or "desired width" of this thumbnail, relative to each other, if one of them is not given  */
        if (!$desired_height) $desired_height = floor($height * ($desired_width / $width));
        if (!$desired_width) $desired_width = floor($width * ($desired_height / $height));

        /* create a new, "virtual" image */
        $virtual_image = imagecreatetruecolor($desired_width, $desired_height);

        /* copy source image at a resized size */
        imagecopyresized($virtual_image, $resource, 0, 0, 0, 0, $desired_width, $desired_height, $width, $height);

        if ($type === IMAGETYPE_GIF)
            imagegif($virtual_image, $dest);
        else if ($type === IMAGETYPE_PNG)
            imagepng($virtual_image, $dest, 1);
        else if ($type === IMAGETYPE_JPEG)
            imagejpeg($virtual_image, $dest, 100);

        return array(
            'width' => $width,
            'height' => $height,
            'new_width' => $desired_width,
            'new_height' => $desired_height,
            'dest' => $dest
        );
    }

    public function getIsInEditorList()
    {
        $editorSession = Yii::$app->session->get('editor', []);
        return in_array($this->id, $editorSession);
    }


    public function verify()
    {
        $this->verified = true;
        return $this->save();
    }
}
