<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Photo */

$this->title = Yii::t('app/photo', 'Update photo: {nameAttribute}', [
    'nameAttribute' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/place', 'Places'), 'url' => ['/place/index']];
$this->params['breadcrumbs'][] = ['label' => $model->place->name, 'url' => ['/place/view', 'id' => $model->id_place]];
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/photo', 'Photos'), 'url' => ['/place/view', 'id' => $model->id_place, '#' => 'photos']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app/photo', 'Update');
?>
<div class="photo-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
