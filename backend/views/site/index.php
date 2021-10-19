<?php

use yii\grid\GridView;
use yii\helpers\Html;
use yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $previewData array */

$this->title = 'Dashboard';
$this->params['breadcrumbs'][] = $this->title;

?>
<div class="site-index">

    <div class="row">
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-aqua">
                <div class="inner">
                    <h3><?= $previewData['places'] ?></h3>

                    <p>Places</p>
                </div>
                <div class="icon">
                    <i class="ion ion-bag"></i>
                </div>
                <a href="<?= Url::to(['/place/index']) ?>" class="small-box-footer">More info <i
                            class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-green">
                <div class="inner">
                    <h3><?= $previewData['photos'] ?></h3>

                    <p>Photos</p>
                </div>
                <div class="icon">
                    <i class="ion ion-stats-bars"></i>
                </div>
                <a href="<?= Url::to(['/place/index']) ?>" class="small-box-footer">More info <i
                            class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <!-- ./col -->
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-yellow">
                <div class="inner">
                    <h3><?= $previewData['users'] ?></h3>

                    <p>Users</p>
                </div>
                <div class="icon">
                    <i class="ion ion-person-add"></i>
                </div>
                <a href="<?= Url::to(['/user/index']) ?>" class="small-box-footer">More info <i
                            class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
        <div class="col-lg-3 col-xs-6">
            <!-- small box -->
            <div class="small-box bg-red">
                <div class="inner">
                    <h3><?= $previewData['photos_unverified'] ?></h3>

                    <p>Unverified photos</p>
                </div>
                <div class="icon">
                    <i class="ion ion-pie-graph"></i>
                </div>
                <a href="#" class="small-box-footer">More info <i class="fa fa-arrow-circle-right"></i></a>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xs-12">
            <div class="user-index box box-primary">
                <div class="box-header with-border">
                    <h3>Fotografie ke schválení</h3>
                </div>
                <div class="box-body table-responsive no-padding">
                    <?= GridView::widget([
                        'dataProvider' => $unverifiedPhotoProvider,
                        'filterModel' => $unverifiedPhotoSearchModel,
                        'columns' => [
                            [
                                'attribute' => 'id_file',
                                'format' => 'html',
                                'value' => function ($model) {
                                    return Html::img($model->file->url, ['style' => 'max-width: 50px; max-height: 50px']);
                                },
                                'filter' => false,
                            ],
                            'id',
                            'name',
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
                                    return Yii::$app->formatter->asDatetime($model->captured_at, 'Y');
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

        <div class="col-lg-6 col-xs-12">

        </div>
    </div>

</div>
