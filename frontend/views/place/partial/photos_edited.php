<?php
/* @var $this \frontend\components\FrontendView */

/* @var $photos array */

use yii\helpers\Url;

?>

<div class="horizontal-scroll-box">
    <?php foreach ($photos as $photo): ?>
            <div class="card">
                <div class="card-image">
                    <?= \yii\helpers\Html::img($photo['thumbnail_url'], [
                        'data-url-original' => $photo['original_url']
                    ]) ?>
                </div>
                <div class="badge-box">
                       <span data-badge-caption=""
                             class="new badge newest"><?= Yii::$app->formatter->asDate($photo['photo_1']['captured_at'], 'Y') ?></span>
                    <span data-badge-caption=""
                          class="new badge newest"><?= Yii::$app->formatter->asDate($photo['photo_2']['captured_at'], 'Y') ?></span>
                </div>
                <div class="card-content">
                    <p>
                        <?= \yii\helpers\Html::a($photo['user']['name'], Url::to(['/user/public-profile', 'id' => $photo['user']['id']]), [
                            "data-pjax" => 0,
                        ]) ?>
                    </p>
                </div>
            </div>
    <?php endforeach; ?>
</div>
