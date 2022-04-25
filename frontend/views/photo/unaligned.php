<?php
/* @var $photo \common\models\Photo */

use yii\widgets\ListView;

?>
<section class="container white-box main-info">
    <div id="unaligned-photos-wrapper">
        <div class="header">
            <h2><?= Yii::t('app/photo', 'Unaligned photos') ?></h2>
            <!-- <i class="material-icons modal-close-btn">close</i> -->
        </div>

        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'id' => 'unaligned-photos-list-view',
            'layout' => "{items}\n{summary}\n{pager}",
            'itemView' => function ($model, $key, $index, $widget) {
                return $this->render('partial/align_photo', ['photo' => $model]);
            },
            'pager' => [
                'maxButtonCount' => 5,
            ],
        ]);
        ?>

        <!-- <div class="actions">
            <a href="<?= \yii\helpers\Url::to('/photo/add') ?>" class="btn brn-default next-btn open-in-modal">Nahrát další</a>
        </div> -->

    </div>
</section>


<?php
$this->registerJS(
    <<<JS
    actualMarker.setMap(null);
JS
);
