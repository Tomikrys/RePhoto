<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Place */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/place', 'Places'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="place-view box box-primary">
    <div class="box-header">
        <p>
            <?= Html::a(Yii::t('app/place', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary  btn-flat']) ?>
            <?= Html::a(Yii::t('app/place', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger btn-flat',
                'data' => [
                    'confirm' => Yii::t('app/place', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </div>
    <div class="box-body table-responsive no-padding">

        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'name',
                'description:ntext',
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
                    'attribute' => 'created_at',
                    'value' => function ($model) {
                        return Yii::$app->formatter->asDatetime($model->created_at);
                    },
                ],
                [
                    'attribute' => 'updated_at',
                    'value' => function ($model) {
                        return Yii::$app->formatter->asDatetime($model->updated_at);
                    },
                ],
            ],
        ]) ?>
    </div>
</div>

<div class="place-view box box-primary">
    <div class="box-header">
        <div id="photos">
            <h2><?= Html::encode(Yii::t('app/photo', 'Photos')) ?></h2>
            <p>
                <?= Html::a(Yii::t('app/photo', 'Create photo'), ['/photo/create', 'id_place' => $model->id], ['class' => 'btn btn-success']) ?>
            </p>

            <div class="box-body table-responsive no-padding">
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'filterModel' => $searchModel,
                    'columns' => [
                        [
                            'attribute' => 'id_file',
                            'format' => 'html',
                            'value' => function ($model) {
                                return Html::img($model->file->url);
                            },
                            'filter' => false,
                        ],
                        'id',
                        'name',
                        //'description:ntext',
                        //'latitude',
                        //'longitude',
                        //'id_place',

                        //'exif_json:ntext',
                        [
                            'attribute' => 'aligned',
                            'format' => 'boolean',
                        ],
                        [
                            'attribute' => 'verified',
                            'format' => 'boolean',
                        ],
                        //'visible',
                        [
                            'attribute' => 'id_user',
                            'format' => 'html',
                            'value' => function ($model) {
                                return $model->user->userPreview;
                            },
                            'filter' => \yii\helpers\ArrayHelper::map(\common\models\User::find()->all(), 'id', 'userPreview'),
                        ],
                        [
                            'attribute' => 'captured_at',
                            'value' => function ($model) {
                                return Yii::$app->formatter->asDatetime($model->captured_at);
                            },
                        ],
                        [
                            'attribute' => 'updated_at',
                            'value' => function ($model) {
                                return Yii::$app->formatter->asDatetime($model->updated_at);
                            },
                        ],


                        [
                            'class' => 'yii\grid\ActionColumn',
                            'template' => '{verify} {view} {update} {delete}',
                            'buttons' => [
                                'verify' => function ($url, $model, $key) {
                                    if ($model->verified) {
                                        return null;
                                    }

                                    $title = Yii::t('yii', 'Verify');
                                    return Html::a('<span class="glyphicon glyphicon-check"></span>', Url::to(['photo/verify', 'id' => $model->id]), [
                                        'title' => $title,
                                        'aria-label' => $title,
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to verify this item?'),
                                    ]);
                                },
                                'view' => function ($url, $model, $key) {     // render your custom button
                                    $title = Yii::t('yii', 'View');
                                    return Html::a('<span class="glyphicon glyphicon-eye-open"></span>', Url::to(['photo/view', 'id' => $model->id]), [
                                        'title' => $title,
                                        'aria-label' => $title,
                                        'data-pjax' => '0',
                                    ]);
                                },
                                'update' => function ($url, $model, $key) {     // render your custom button
                                    $title = Yii::t('yii', 'Update');
                                    return Html::a('<span class="glyphicon glyphicon-pencil"></span>', Url::to(['photo/update', 'id' => $model->id]), [
                                        'title' => $title,
                                        'aria-label' => $title,
                                        'data-pjax' => '0',
                                    ]);
                                },
                                'delete' => function ($url, $model, $key) {     // render your custom button
                                    $title = Yii::t('yii', 'Delete');
                                    return Html::a('<span class="glyphicon glyphicon-trash"></span>', Url::to(['photo/delete', 'id' => $model->id]), [
                                        'data-confirm' => Yii::t('yii', 'Are you sure you want to delete this item?'),
                                        'data-method' => 'post',
                                        'title' => $title,
                                        'aria-label' => $title,
                                        'data-pjax' => '0',
                                    ]);
                                },
                            ]
                        ],
                    ],
                ]); ?>
            </div>
        </div>
    </div>
</div>
