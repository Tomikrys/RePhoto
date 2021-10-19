<?php

use common\models\User;
use yii\grid\GridView;
use yii\helpers\Html;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\search\UserSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/user', 'Users');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-index box box-primary">
    <div class="box-header with-border">
        <?= Html::a(Yii::t('app/user', 'Create user'), ['create'], ['class' => 'btn btn-success btn-flat']) ?>
    </div>
    <div class="box-body table-responsive no-padding">
        <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'layout' => "{items}\n{summary}\n{pager}",
            'columns' => [
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

                ['class' => 'yii\grid\ActionColumn'],
            ],
        ]); ?>
    </div>
</div>

