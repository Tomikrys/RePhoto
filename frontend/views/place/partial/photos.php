<?php
/* @var $this \frontend\components\FrontendView */

/* @var $photos array */

use yii\helpers\Url;

?>

<div class="horizontal-scroll-box">
    <?php foreach ($photos as $photo): ?>
            <div class="card photo"
                 data-id="<?= $photo['id'] ?>"
                 data-editor="<?= $photo['in_editor'] ?>"
            >
                <div class="card-image">
                    <?= \yii\helpers\Html::img($photo['thumbnail_url'], [
                        'data-url-original' => $photo['original_url']
                    ]) ?>
                </div>
                <div class="badge-box">
                      <span data-badge-caption=""
                            class="new badge newest"><?= Yii::$app->formatter->asDate($photo['captured_at'], 'Y') ?></span>
                </div>
                <div class="card-content">
                    <p>
                        <?= \yii\helpers\Html::a($photo['user']['name'], Url::to(['/user/public-profile', 'id' => $photo['user']['id']]), [
                            "data-pjax" => 0,
                        ]) ?>
                    </p>
                </div>
                <div class="card-action">
                    <?= \yii\helpers\Html::a('add to editor', Url::to(['/photo/add-to-editor-list', 'id' => $photo['user']['id']]), [
                        'class' => 'toggle-editor-btn add-btn',
                        'style' => !$photo['in_editor'] ?: 'display: none;',
                    ]) ?>
                    <?= \yii\helpers\Html::a('remove from editor', Url::to(['/photo/remove-from-editor-list', 'id' => $photo['user']['id']]), [
                        'class' => 'toggle-editor-btn remove-btn',
                        'style' => $photo['in_editor'] ?: 'display: none;',
                    ]) ?>
                </div>
            </div>
    <?php endforeach; ?>
</div>

