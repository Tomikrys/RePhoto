<?php

namespace frontend\models\search;

use common\models\Photo;
use common\models\PhotoWishList;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * PhotoSearch represents the model behind the search form about `common\models\Photo`.
 */
class PhotoSearch extends Photo
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'latitude', 'longitude', 'id_user', 'created_at', 'updated_at'], 'integer'],
            [['name', 'description'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function searchUnpublished($params)
    {
        $query = Photo::find()
            ->andWhere([
                'visible' => false,
                'id_user' => Yii::$app->user->id,
            ]);

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            //'id_user' => $this->id_user,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description]);

        return $dataProvider;
    }

    public function searchFavorite($id_user)
    {
        $subQuery = PhotoWishList::find()
            ->andWhere(['id_user' => $id_user])
            ->andWhere('photo.id=photo_wish_list.id_photo');

        $query = Photo::find()
            ->andWhere(['visible' => true])
            ->andWhere(['exists', $subQuery])
            ->orderBy('id desc');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        return $dataProvider;
    }

    public function searchForSideNav($bounds)
    {
        $query = Photo::find()
            ->andWhere([
                'and',
                ['between', 'latitude', $bounds['sw']['lat'], $bounds['ne']['lat']],
                ['between', 'longitude', $bounds['sw']['lng'], $bounds['ne']['lng']],
            ])
            ->andWhere(['visible' => true])
            ->with('loggedUserWishList')
            ->orderBy('id desc');

        $photos = $query->all();

        $array = [];
        foreach ($photos as $photo) {
            /* @var $photo Photo */
            $array[] = [
                'id' => $photo->id,
                'name' => $photo->name,
                'latitude' => $photo->latitude,
                'longitude' => $photo->longitude,
                'wishlisted' => $photo->loggedUserWishList !== null,
                'thumbnailUrl' => $photo->getThumbnailUrl(),
                'inEditor' => $photo->getIsInEditorList(),
            ];
        }

        return $array;
    }

    public function searchUploaded($id_user, $params)
    {
        $this->setAttributes($params);

        $query = Photo::find()
            ->andWhere(['id_user' => $id_user])
            ->orderBy('id desc');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 12,
            ]
        ]);

        return $dataProvider;
    }
}
