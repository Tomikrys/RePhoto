<?php

namespace frontend\models;

use yii\base\Model;

/**
 * Password reset form
 */
class PlaceFilter extends Model
{
    public $cat;
    public $query;
    public $year_min;
    public $year_max;
    public $lat;
    public $lng;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['lat', 'lng', 'year_min', 'year_max'], 'number'],
            [['id_categories', 'query'], 'string'],
        ];
    }
}
