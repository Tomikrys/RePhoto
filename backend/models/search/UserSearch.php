<?php

namespace backend\models\search;

use common\models\User;
use Yii;
use yii\data\ActiveDataProvider;
use yii\db\Expression;
use yii\helpers\ArrayHelper;

/**
 * PlaceSearch represents the model behind the search form about `common\models\Place`.
 */
class UserSearch extends User
{
    public $name;
    public $fb_profile;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'name', 'email'], 'string'],
            [['role', 'status', 'fb_profile', 'email_verified'], 'integer'],

        ];
    }

    public function attributeLabels()
    {
        return ArrayHelper::merge(parent::attributeLabels(), [
            'fb_profile' => Yii::t('app/user', 'Facebook linked'),
            'name' => Yii::t('app/user', 'Name'),
        ]);
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
        $query = User::find()->select(['*', 'name' => 'CONCAT(first_name, " " , last_name)']);


        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $sort = $dataProvider->getSort();
        $sort->attributes = ArrayHelper::merge($sort->attributes, [
            'name' => [
                'asc' => [
                    'first_name' => SORT_ASC,
                    'last_name' => SORT_ASC
                ],
                'desc' => [
                    'first_name' => SORT_DESC,
                    'last_name' => SORT_DESC
                ],
                'default' => SORT_ASC
            ],
            'fb_profile' => [
                'asc' => [
                    'fb_id' => SORT_ASC,
                ],
                'desc' => [
                    'fb_id' => SORT_DESC,
                ],
                'default' => SORT_ASC
            ],
        ]);

        $dataProvider->setSort($sort);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'role' => $this->role,
            'status' => $this->status,
            'email_verified' => $this->email_verified,
        ]);


        $query->andFilterWhere([$this->fb_profile ? 'is not' : 'is', 'fb_id', new Expression('NULL')]);

        $query->andFilterHaving(['like', 'name', $this->name]);
        $query->andFilterWhere(['like', 'email', $this->email]);

        return $dataProvider;
    }
}
