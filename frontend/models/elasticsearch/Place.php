<?php

namespace frontend\models\elasticsearch;

use yii\elasticsearch\ActiveDataProvider;
use yii\elasticsearch\ActiveRecord;
use yii\elasticsearch\Query;
use yii\helpers\ArrayHelper;

class Place extends ActiveRecord
{
    /**
     * Set (update) mappings for this model
     */
    public static function updateMapping()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->setMapping(static::index(), static::type(), static::mapping());
    }

    /**
     * @return array This model's mapping
     */
    public static function mapping()
    {
        return [
            static::type() => [
                'properties' => [
                    'id' => ['type' => 'integer'],
                    'location' => ['type' => 'geo_point'],
                    'id_category' => ['type' => 'integer'],
                    'photo_captured_at' => [
                        'type' => 'date',
                        "format" => "yyyy-MM-dd HH:mm:ss",
                    ],
                ],
            ],
        ];
    }

    public static function refreshData()
    {
        // delete data from elastic search
        static::deleteIndex();
        static::createIndex();

        // transfer data from sql database
        foreach (\common\models\Place::find()->each() as $place) {
            $model = new static();
            $model->id = $place->id;
            $model->id_category = $place->id_category;
            $model->photo_captured_at = array_values(ArrayHelper::map($place->getPhotos()->select(['id', 'captured_at'])->asArray()->all(), 'id', 'captured_at'));
            $model->location = ['lat' => (double)$place->latitude, 'lon' => (double)$place->longitude];
            if (!$model->save()) {
                return false;
            }
        }

        return true;
    }

    /**
     * Delete this model's index
     */
    public static function deleteIndex()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->deleteIndex(static::index(), static::type());
    }

    /**
     * Create this model's index
     */
    public static function createIndex()
    {
        $db = static::getDb();
        $command = $db->createCommand();
        $command->createIndex(static::index(), [
            //'settings' => [ /* ... */],
            'mappings' => static::mapping(),
            //'warmers' => [ /* ... */ ],
            //'aliases' => [ /* ... */ ],
            //'creation_date' => '...'
        ]);
    }

    public static function getAggregatedPoints(array $boundingBox, int $zoom = null, array $params)
    {
        // categories
        $cats = [];
        if ($params['bu']) {
            $cats[] = \common\models\Place::CAT_BUILDINGS;
        }
        if ($params['na']) {
            $cats[] = \common\models\Place::CAT_NATURE;
        }
        if ($params['un']) {
            $cats[] = \common\models\Place::CAT_UNKNOWN;
        }


        $query = new Query();
        $query->from('places')
            ->limit(0)
            ->andWhere(['id_category' => $cats])
            ->andWhere(['>=', 'photo_captured_at', $params['ymin'] . '-01-01 00:00:00'])
            ->andWhere(['<=', 'photo_captured_at', $params['ymax'] . '-12-31 23:59:59']);

        if ($params['map']) {
            $query->addAggregate('zoom-in', [
                "filter" => [
                    "geo_bounding_box" => [
                        "location" => [
                            "top_left" => [
                                'lat' => (double)$boundingBox['nw']['lat'],
                                'lon' => (double)$boundingBox['nw']['lon'],
                            ],
                            "bottom_right" => [
                                'lat' => (double)$boundingBox['se']['lat'],
                                'lon' => (double)$boundingBox['se']['lon'],
                            ],
                        ],
                    ],
                ],
                "aggregations" => [
                    "zoom1" => [
                        "geohash_grid" => [
                            "field" => "location",
                            "precision" => static::getPrecisionFromZoom($zoom),
                        ],
                        'aggregations' => [
                            "centroid" => [
                                'geo_centroid' => [
                                    "field" => "location",
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        } else {
            $query->addAggregate('zoom1', [
                "geohash_grid" => [
                    "field" => "location",
                    "precision" => static::getPrecisionFromZoom($zoom),
                ],
                'aggregations' => [
                    "centroid" => [
                        'geo_centroid' => [
                            "field" => "location",
                        ],
                    ],
                ],
            ]);
        }

        $provider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => [
                'pageSize' => 10,
            ]
        ]);

        if ($params['map']){
            $data = ArrayHelper::map($provider->getAggregation('zoom-in')['zoom1']['buckets'], 'key', 'centroid');
        } else {
            $data = ArrayHelper::map($provider->getAggregation('zoom1')['buckets'], 'key', 'centroid');
        }

        return array_values($data);
    }

    public static function getPrecisionFromZoom(int $zoom)
    {
        if ($zoom <= 4) {
            return 1;
        } else if ($zoom <= 6) {
            return 2;
        } else if ($zoom <= 8) {
            return 3;
        } else if ($zoom <= 10) {
            return 4;
        } else if ($zoom <= 12) {
            return 5;
        } else if ($zoom <= 14) {
            return 6;
        } else if ($zoom <= 16) {
            return 7;
        } else if ($zoom <= 18) {
            return 8;
        } else if ($zoom <= 20) {
            return 9;
        }

        return 10;
    }

    /**
     * @return array the list of attributes for this record
     */
    public function attributes()
    {
        // path mapping for '_id' is setup to field 'id'
        return ['id', 'location', 'id_category', 'photo_captured_at'];
    }
}