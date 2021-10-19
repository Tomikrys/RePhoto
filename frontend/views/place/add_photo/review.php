<?php
/* @var $this \frontend\components\FrontendView */
/* @var $place \common\models\Place */
/* @var $photoNew \common\models\Photo */
/* @var $photoOld \common\models\Photo */

$this->title = 'Align new rephoto';
$this->h1 = $this->title;
$this->bodyClasses[] = 'place-add-photo';

\frontend\assets\PhotoAlignerAsset::register($this);

?>

<?= $this->render('partial/stepper', ['active' => 3]) ?>


    <section class="container">
        <div class="white-box">
            <h2>Review automatic alignment</h2>
            <p>To help you with adding new rephoto, your image was aligned automaticly with historical photo. Please
                check if this alignment is correct and then continue in upload process by clicking <i>confirm button</i>
                or align photos manually after clicking on <i>manual align</i> button bellow images.</a></p>
        </div>
    </section>

    <section class="container">

        <div class="row">
            <div class="col-md-12 valign-wrapper review-image-box">
                <div class="slider">
                    <div>
                        <img id="old-photo" class="" src="<?= $photoOld->getUrl() ?>">
                    </div>
                    <div class="new-photo-wrapper">
                        <img id="new-photo" class="" src="<?= $transformedImagePath ?>">
                    </div>
                    <div class="handle">
                        <span class="line"></span>

                    </div>
                </div>
            </div>
        </div>

        <div class="actions">
            <a href="<?= \yii\helpers\Url::to(['/place/upload-photo', 'id_place' => $place->id]) ?>" class="btn btn-back">Upload
                another photo</a>

            <a href="<?= \yii\helpers\Url::to(['/place/align-photo', 'id_photo' => $photoNew->id]) ?>"
               class="btn btn-back btn-flex-right">Manual align</a>

            <a href="<?= \yii\helpers\Url::to(['/place/confirm-photo', 'id_photo' => $photoNew->id, 'aligned' => $transformedImagePath]) ?>"
               class="btn waves-effect waves-light">Confirm align</a>
        </div>

    </section>

<?php $this->registerJs(<<<JS
    $(document).on('mousemove', '.slider', function(e){
        var x = e.clientX - parseInt($('.slider').offset().left);
        
        var width = $(this).width();
        var percentage = parseInt(x*100/width);
        console.log(percentage);
        $('.new-photo-wrapper').width(percentage + "%");
        $('.handle').css('left', percentage + "%");
    });
JS
);