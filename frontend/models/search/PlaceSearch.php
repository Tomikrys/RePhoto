<?php

namespace frontend\models\search;

use common\models\Photo;
use common\models\PhotoWishList;
use common\models\Place;
use Yii;
use yii\data\ActiveDataProvider;
use yii\helpers\ArrayHelper;

/**
 * PlaceSearch represents the model behind the search form about `common\models\Place`.
 */
class PlaceSearch extends Place
{
    const PER_PAGE = 6;
    public $ymin;
    public $ymax;
    public $bounds;
    public $na;
    public $bu;
    public $un;
    public $page = 1;
    public $zoom;
    public $map;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['ymin', 'ymax', 'page'], 'integer'],
            [['na', 'bu', 'un', 'map'], 'integer'],
            [['bounds', 'zoom'], 'safe'],
        ];
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'bu' => Yii::t('app/map', 'Building'),
            'na' => Yii::t('app/map', 'Nature'),
            'un' => Yii::t('app/map', 'Unknown'),
        ]);
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

    public function getMapData($params)
    {
        $data = [];
        $this->setAttributes($params);

        if (!$this->validate()) {

        }

        //$data['markers'] = $this->page == 1 ? $this->getAllPlacesPoition() : [];
        $data['markers'] = $this->page == 1 ? \frontend\models\elasticsearch\Place::getAggregatedPoints($this->bounds, $this->zoom, $params) : [];
        $data['places'] = $this->getPlacesPreview();
        $data['lastFetch'] = count($data['places']) != static::PER_PAGE;
        $data['page'] = $this->page;

        return $data;
    }

    public function getPlacesPreview(int $limit = 6)
    {
        $query = $this->getPlaceQuery();
        $query->limit($limit);
        $query->offset(static::PER_PAGE * ($this->page - 1));

        $places = $query->all();

        $items = [];
        foreach ($places as $place) {
            /* @var $place Place */
// TODO 'newest_photo' => [ 'id' => $place->oldestPhoto->id,
            $items[] = [
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
        }

        return $items;
    }

    protected function getPlaceQuery()
    {
        $query = Place::find()
            ->with('oldestPhoto')
            ->with('newestPhoto')
            ->with('placeSavedForLoggedUser')
            ->orderBy('id desc');


        if ($this->map) {
            $query->andWhere([
                'and',
                ['between', 'place.latitude', $this->bounds['se']['lat'], $this->bounds['nw']['lat']],
                ['between', 'place.longitude', $this->bounds['nw']['lon'], $this->bounds['se']['lon']],
            ]);
        }

        $subquery = null;
        if ($this->ymin !== null && $this->ymax !== null) {
            $subquery = Photo::find()
                ->andWhere('photo.id_place=place.id')
                ->andWhere([
                    'between',
                    'captured_at',
                    $this->ymin . '-01-01',
                    $this->ymax . '-12-31',
                ]);
        } else if ($this->ymin !== null) {
            $subquery = Photo::find()->andWhere([
                '>=',
                'captured_at',
                $this->ymin . '-01-01',
            ]);
        } else if ($this->ymax !== null) {
            $subquery = Photo::find()->andWhere([
                '<=',
                'captured_at',
                $this->ymax . '-12-31',
            ]);
        }

        if ($subquery !== null) {
            $query->andWhere(['exists', $subquery]);
        }

        // categories
        $cats = [];
        if ($this->bu) {
            $cats[] = Place::CAT_BUILDINGS;
        }
        if ($this->na) {
            $cats[] = Place::CAT_NATURE;
        }
        if ($this->un) {
            $cats[] = Place::CAT_UNKNOWN;
        }

        $query->andWhere(['id_category' => $cats]);

        return $query;
    }

    public function getAllPlacesPoition()
    {
        $query = $this->getPlaceQuery();
        $data = $query->select(['place.id', 'place.latitude', 'place.longitude'])
            ->asArray()->all();

        foreach ($data as &$item) {
            unset($item['oldestPhoto'], $item['newestPhoto'], $item['placeSavedForLoggedUser']);
        }

        return $data;
    }
}
