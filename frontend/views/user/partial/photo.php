<?php
/* @var $model \common\models\Photo */
?>

<div class="card">
    <div class="card-image">
        <?= \yii\helpers\Html::img($model->getThumbnailUrl(), [
            'data-url-original' => ''
        ]) ?>
    </div>
    <div class="card-content">
        <p><?= $model->name ?></p>
    </div>
    <div class="card-action">
        <a href="<?= \yii\helpers\Url::to(['/place/view', 'id' => $model->id_place]) ?>" class="teal-text"
           data-pjax="0">detail</a>
    </div>
</div>
