<?php

use common\models\User;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\User */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/user', 'Users'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="user-view box box-primary">
    <div class="box-header">
        <?= Html::a(Yii::t('app/user', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-flat']) ?>
        <?= Html::a(Yii::t('app/user', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger btn-flat',
            'data' => [
                'confirm' => Yii::t('app/user', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </div>
    <div class="box-body table-responsive no-padding">
        <?= DetailView::widget([
            'model' => $model,
            'attributes' => [
                'id',
                'name',
                'email:email',
                [
                    'attribute' => 'email_verified',
                    'format' => 'html',
                    'value' => function ($model) {
                        return Html::tag('span', Yii::t('app', $model->email_verified ? 'yes' : 'no'), [
                            'class' => ['label', $model->email_verified ? 'label-success' : 'label-danger']
                        ]);
                    },
                    'filter' => [
                        1 => Yii::t('app', 'yes'),
                        0 => Yii::t('app', 'no'),
                    ],
                ],
                [
                    'attribute' => 'role',
                    'value' => function ($model) {
                        return $model->roleName;
                    },
                    'filter' => [
                        User::ROLE_ADMIN => Yii::t('app/user', 'admin'),
                        User::ROLE_USER => Yii::t('app/user', 'user'),
                    ],
                ],
                [
                    'attribute' => 'fb_profile',
                    'format' => 'html',
                    'value' => function ($model) {
                        $hasProfile = $model->fb_id != null;
                        return Html::tag('span', Yii::t('app', $hasProfile ? 'yes' : 'no'), [
                            'class' => ['label', $hasProfile ? 'label-success' : 'label-danger']
                        ]);
                    },
                    'filter' => [
                        1 => Yii::t('app', 'yes'),
                        0 => Yii::t('app', 'no'),
                    ],

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
