<?php

namespace frontend\models;

use common\models\File;
use common\models\Photo;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

/**
 * UploadForm is the model behind the contact form.
 */
class UploadForm extends Model
{
    /**
     * @var UploadedFile
     */
    public $file;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['file'], 'required'],
            [['file'], 'file', 'skipOnEmpty' => false, 'extensions' => 'png, jpg'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'file' => 'file',
        ];
    }

    public function saveFile(int $id_place, $aligned = false)
    {
        if ($this->validate()) {
            $trans = Yii::$app->db->beginTransaction();
            $file = new File();
            $file->id_user = Yii::$app->user->id;
            $file->extension = $this->file->extension;

            if ($file->save()) {
                $photo = new Photo();
                $photo->setAttributesFromFile($this->file);
                $photo->setExifDataFromFile($this->file);
                $photo->id_file = $file->id;
                $photo->id_place = $id_place;
                $photo->aligned = (int)$aligned;
                $photo->visible = 0;

                if ($photo->save()) {
                    # save file to uploads folder
                    if ($this->file->saveAs(\Yii::getAlias('@uploads') . '/' . $photo->id . '.' . $file->extension)) {
                        $photo->createThumbnail();
                        $trans->commit();
                        return $photo->id;
                    }
                }
            }

            $trans->rollback();
        }

        return false;
    }
}
