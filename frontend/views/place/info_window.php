<?php
/* @var $place \frontend\models\elasticsearch\Place */
?>

<div class="card info-window">
    <div class="card-image">
        <img class="oldest partial" src="<?= $place['oldest_photo']['thumbnail_url'] ?>">
        <img class="newest" src="<?= $place['newest_photo']['thumbnail_url'] ?>">
        <span data-badge-caption=""
              class="new badge oldest"><?= Yii::$app->formatter->asDate($place['oldest_photo']['captured_at'], 'Y') ?></span>
        <span data-badge-caption=""
              class="new badge newest"><?= Yii::$app->formatter->asDate($place['newest_photo']['captured_at'], 'Y') ?></span>
    </div>
    <div class="card-content">
        <p><?= $place['name'] ?></p>

        <a href="<?= \yii\helpers\Url::to(['/place/view', 'id' => $place['id']]) ?>" class="btn btn-primary"
           data-pjax="0">detail</a>
    </div>
</div>