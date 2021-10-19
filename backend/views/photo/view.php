<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model common\models\Photo */

$this->title = $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/place', 'Places'), 'url' => ['/place/index']];
$this->params['breadcrumbs'][] = ['label' => $model->place->name, 'url' => ['/place/view', 'id' => $model->id_place]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/photo', 'Photos'), 'url' => ['/place/view', 'id' => $model->id_place, '#' => 'photos']];
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
                'verified',
                'captured_at',
                'name',
                'description:ntext',
                'latitude',
                'longitude',
                'id_user',
                'id_file',
                'id_place',
                'aligned',
                'visible',
                'exif_json:ntext',
                'created_at',
                'updated_at',
            ],
        ]) ?>

    </div>
</div>
