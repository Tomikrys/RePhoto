<?php

namespace common\models;

use yii\db\ActiveRecord;
use yii\helpers\Url;

/**
 * This is the model class for table "file".
 *
 * @property integer $id
 * @property integer $id_user
 * @property string $extension
 * @property integer $created_at
 * @property integer $updated_at
 *
 * @property User $user
 * @property Photo[] $photos
 * @property Rephoto[] $rephotos
 */
class File extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'file';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_user', 'extension'], 'required'],
            [['id_user', 'created_at', 'updated_at'], 'integer'],
            [['extension'], 'string', 'max' => 32],
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
            'id_user' => 'Id User',
            'extension' => 'Extension',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
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
    public function getPhotos()
    {
        return $this->hasMany(Photo::className(), ['id_file' => 'id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRephotos()
    {
        return $this->hasMany(Rephoto::className(), ['id_file' => 'id']);
    }

    public function getPath()
    {
        return \Yii::getAlias('@root/uploads/' . $this->id . '.' . $this->extension);
    }

    public function getUrl()
    {
        return '/uploads/' . $this->id . '.' . $this->extension;
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
}
