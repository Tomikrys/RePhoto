<?php

namespace common\models;

/**
 * This is the model class for table "rephoto".
 *
 * @property integer $id
 * @property integer $id_file
 * @property integer $id_photo_1
 * @property integer $id_photo_2
 * @property integer $created_at
 *
 * @property File $file
 * @property Photo $photo1
 * @property Photo $photo2
 */
class Rephoto extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'rephoto';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_file', 'id_photo_1', 'id_photo_2', 'created_at'], 'required'],
            [['id_file', 'id_photo_1', 'id_photo_2', 'created_at'], 'integer'],
            [['id_file'], 'exist', 'skipOnError' => true, 'targetClass' => File::className(), 'targetAttribute' => ['id_file' => 'id']],
            [['id_photo_1'], 'exist', 'skipOnError' => true, 'targetClass' => Photo::className(), 'targetAttribute' => ['id_photo_1' => 'id']],
            [['id_photo_2'], 'exist', 'skipOnError' => true, 'targetClass' => Photo::className(), 'targetAttribute' => ['id_photo_2' => 'id']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'id_file' => 'Id File',
            'id_photo_1' => 'Id Photo 1',
            'id_photo_2' => 'Id Photo 2',
            'created_at' => 'Created At',
        ];
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
    public function getPhoto1()
    {
        return $this->hasOne(Photo::className(), ['id' => 'id_photo_1']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPhoto2()
    {
        return $this->hasOne(Photo::className(), ['id' => 'id_photo_2']);
    }
}
