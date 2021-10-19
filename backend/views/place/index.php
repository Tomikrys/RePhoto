<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\PlaceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/place', 'Places');
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="place-index box box-primary">
    <div class="box-header with-border">
        <?= Html::a(Yii::t('app/place', 'Create place'), ['create'], ['class' => 'btn btn-success btn-flat']) ?>
    </div>
    <div class="box-body table-responsive no-padding">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{items}\n{summary}\n{pager}",
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'id',
                'name',
                //'description:ntext',
                'latitude',
                'longitude',
                [
                    'attribute' => 'id_user',
                    'format' => 'html',
                    'value' => function ($model) {
                        return $model->userPreview;
                    },
                    'filter' => \yii\helpers\ArrayHelper::map(\common\models\User::find()->all(), 'id', 'userPreview'),
                ],
                [
                    'attribute' => 'id_category',
                    'value' => function ($model) {
                        return $model->categoryName;
                    },
                    'filter' => \common\models\Place::getCategories(),
                ],
                [
                    'attribute' => 'updated_at',
                    'value' => function ($model) {
                        return Yii::$app->formatter->asDatetime($model->updated_at);
                    },
                ],

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
</div>
