<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Place */

$this->title = Yii::t('app/place', 'Create place');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/place', 'Places'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="place-create">

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
