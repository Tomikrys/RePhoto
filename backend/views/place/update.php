<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model common\models\Place */

$this->title = Yii::t('app/place', 'Update place: {nameAttribute}', [
    'nameAttribute' => $model->name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/place', 'Places'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app/place', 'Update');
?>
<div class="place-update">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
