<?php

namespace backend\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use common\models\Photo;

/**
 * PhotoSearch represents the model behind the search form of `common\models\Photo`.
 */
class PhotoSearch extends Photo
{
    public $unverifiedOnly = false;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'verified', 'id_user', 'id_file', 'id_place', 'aligned', 'visible', 'created_at', 'updated_at'], 'integer'],
            [['captured_at', 'name', 'description', 'exif_json'], 'safe'],
            [['latitude', 'longitude'], 'number'],
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
    public function search($params)
    {
        $query = Photo::find();

        // add conditions that should always apply here
        if ($this->unverifiedOnly){
            $query->andWhere(['verified' => 0]);
        }


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'verified' => $this->verified,
            'captured_at' => $this->captured_at,
            'latitude' => $this->latitude,
            'longitude' => $this->longitude,
            'id_user' => $this->id_user,
            'id_file' => $this->id_file,
            'id_place' => $this->id_place,
            'aligned' => $this->aligned,
            'visible' => $this->visible,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'exif_json', $this->exif_json]);

        return $dataProvider;
    }
}
