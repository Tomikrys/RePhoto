<?php

namespace common\models;

use Yii;

/**
 * This is the model class for table "place_saved".
 *
 * @property integer $id
 * @property integer $id_place
 * @property integer $id_user
 * @property integer $created_at
 *
 * @property Place $place
 * @property User $user
 */
class PlaceSaved extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'place_saved';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_place', 'id_user', 'created_at'], 'required'],
            [['id_place', 'id_user', 'created_at'], 'integer'],
            [['id_place'], 'exist', 'skipOnError' => true, 'targetClass' => Place::className(), 'targetAttribute' => ['id_place' => 'id']],
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
            'id_place' => 'Id Place',
            'id_user' => 'Id User',
            'created_at' => 'Created At',
        ];
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
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'id_user']);
    }
}
