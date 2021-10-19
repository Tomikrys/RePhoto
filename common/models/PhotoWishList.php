<?php

namespace common\models;

use yii\db\ActiveRecord;

/**
 * This is the model class for table "photo_wish_list".
 *
 * @property int $id
 * @property int $id_photo
 * @property int $id_user
 * @property int $created_at
 *
 * @property Photo $photo
 * @property User $user
 */
class PhotoWishList extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'photo_wish_list';
    }

    /**
     * Adds photo to user's wish list.
     * @param int $id_photo photo identification
     * @param int $id_user user identification
     * @return bool successfully processed
     */
    public static function create(int $id_photo, int $id_user)
    {
        # check if photo is already on wish list
        if (static::find()->andWhere(['id_user' => $id_user, 'id_photo' => $id_photo])->exists()) {
            return true;
        }

        # add to wish list
        $connector = new PhotoWishList();
        $connector->setAttributes([
            'id_user' => $id_user,
            'id_photo' => $id_photo,
        ]);

        return $connector->save();
    }

    /**
     * Removes photo from user's wish list.
     * @param int $id_photo photo identification
     * @param int $id_user user identification
     * @return bool successfully processed
     */
    public static function remove(int $id_photo, int $id_user)
    {
        # get wish list model
        $model = static::find()->andWhere(['id_user' => $id_user, 'id_photo' => $id_photo])->one();

        # check if photo is already deleted from wish list
        if (!$model) {
            return true;
        }

        return $model->delete();
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_photo', 'id_user'], 'required'],
            [['id_photo', 'id_user', 'created_at'], 'integer'],
            [['id_photo'], 'exist', 'skipOnError' => true, 'targetClass' => Photo::className(), 'targetAttribute' => ['id_photo' => 'id']],
            [['id_user'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id_user' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_photo' => 'Id Photo',
            'id_user' => 'Id User',
            'created_at' => 'Created At',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhoto()
    {
        return $this->hasOne(Photo::className(), ['id' => 'id_photo']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'id_user']);
    }

    public function behaviors()
    {
        return [
            'timestamp' => [
                'class' => 'yii\behaviors\TimestampBehavior',
                'attributes' => [
                    ActiveRecord::EVENT_BEFORE_INSERT => ['created_at'],
                ],
            ],
        ];
    }
}
