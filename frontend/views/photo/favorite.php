<?php
/* @var $photo \common\models\Photo */
/* @var $this \yii\web\View */


$this->title = "Oblíbené";
$this->h1 = $this->title;

?>

<div class="favorite-photos">
    <?= \yii\widgets\ListView::widget([
        'layout' => "{items}\n{pager}",
        'dataProvider' => $dataProvider,
        'itemView' => 'partial/photoPreview',
    ]); ?>
</div>