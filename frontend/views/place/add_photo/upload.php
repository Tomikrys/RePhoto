<?php
/* @var $this \frontend\components\FrontendView */
/* @var $place \common\models\Place */
/* @var $photos array */
/* @var $photosEdited array */
/* @var $uploadForm \frontend\models\UploadForm */

$this->title = 'Add new rephoto';
$this->h1 = $this->title;
$this->bodyClasses[] = 'place-add-photo';

\frontend\assets\PhotoAlignerAsset::register($this);

?>

<?= $this->render('partial/stepper', ['active' => 1]) ?>

    <section class="container">
        <div class="white-box">
            <h2><?= Yii::t('app/place', 'Upload File') ?></h2>
            <p><?= Yii::t('app/place', 'Upload new photo of') ?> <i><?= $place->name ?></i> <?= Yii::t('app/place', 'here or select one of') ?> <a href="#previous-uploaded"><?= Yii::t('app/place', 'previously uploaded.') ?></a></p>

            <?php $form = \yii\bootstrap\ActiveForm::begin([
                'id' => 'form-profile',
                'options' => [
                    'enctype' => 'multipart/form-data',
                ],
            ]); ?>

            <?= $form->field($uploadForm, 'file')->fileInput()->label(false) ?>

            <div class="actions">
                <button class="btn waves-effect waves-light"><?= Yii::t('app/place', 'Upload') ?></button>
            </div>

            <?php \yii\bootstrap\ActiveForm::end(); ?>
        </div>
    </section>


<?php if (!empty($unpublishedPhotos)): ?>
    <section id="previous-uploaded" class="container">
        <div class="white-box">
            <h2><?= Yii::t('app/place', 'Previously uploaded files') ?></h2>
            <p><?= Yii::t('app/place', 'These images are not aligned and published.') ?></p>
        </div>

        <div class="horizontal-scroll-box">
            <?php foreach ($unpublishedPhotos as $photo): ?>
                <div class="card photo">
                    <div class="card-image">
                        <?= \yii\helpers\Html::img($photo->getThumbnailUrl()) ?>
                    </div>
                    <div class="card-action">
                        <a href="<?= \yii\helpers\Url::to(['/place/review-photo', 'id_photo' => $photo['id']]) ?>"
                           class="teal-text"
                           data-pjax="0"><?= Yii::t('app/place', 'Select') ?></a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
<?php endif; ?>


<?php $this->registerJs(<<<JS
  
JS
);