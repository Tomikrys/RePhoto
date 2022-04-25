<?php
/* @var $photo \common\models\Photo */

use yii\widgets\ListView;

?>
<section class="container main-info white-box">
    <div id="unpublished-photos-wrapper">
        <div class="header">
            <h2><?= Yii::t('app/photo', 'Unpublished photos') ?></h2>
            <!-- <i class="material-icons modal-close-btn">close</i> -->
        </div>

        <?= ListView::widget([
            'dataProvider' => $dataProvider,
            'id' => 'unpublished-photos-list-view',
            'layout' => "{items}\n{summary}\n{pager}",
            'itemView' => function ($model, $key, $index, $widget) {
                return $this->render('partial/photo', ['photo' => $model]);
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
