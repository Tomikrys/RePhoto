<?php
/* @var $photo \common\models\Photo */
/* @var $this \yii\web\View */

\kartik\switchinput\SwitchInputAsset::register($this);
?>


<section class="container white-box">
    <div id="edit-photo-wrapper" class="row">
        <div class="header">
            <h2><?= Yii::t('app/photo', 'Edit photo informations') ?></h2>
            <!-- <i class="material-icons modal-close-btn">close</i> -->
            <!-- <i class="material-icons modal-up-btn" style="display: none">keyboard_arrow_up</i> -->
        </div>

        <div class="col s8">
            <div class="photo">
                <img src="<?= $photo->getThumbnailUrl() ?>">
            </div>
        </div>

        <?php $form = \yii\bootstrap\ActiveForm::begin([
            'id' => 'photo-edit-form',
            'action' => ['/photo/edit', 'id' => $photo->id],
            'options' => [
                'class' => 'col s9',
            ]
        ]); ?>

        <div class="row pt-3">
            <div class="col s7">
                <?= $form->field($photo, 'name') ?>
            </div>
            <div class="col s4">
                <?php echo $form->field($photo, 'captured_at')->textInput() ?>

            </div>
        </div>

        <!-- <div class="row">
        <div class="col s6">
            <?= $form->field($photo, 'latitude') ?>
        </div>

        <div class="col s6">
            <?= $form->field($photo, 'longitude') ?>
        </div>
    </div> -->

        <!-- <div class="row">
        <div class="col s12">
            <?= $form->field($photo, 'description')->textarea() ?>
        </div>
    </div> -->

        <div class="row">
            <div class="col s12">
                <?= $form->field($photo, 'visible')->widget(\kartik\switchinput\SwitchInput::className(), [
                    'pluginOptions' => [
                        'onText' => 'Yes',
                        'offText' => 'No',
                    ]
                ]) ?>
            </div>
        </div>

        <?php $form::end() ?>


        <div class="col s12 actions">
            <a href="<?= \yii\helpers\Url::previous() ?>" class="btn btn-default open-in-modal"><?= Yii::t('app/photo', 'Back') ?></a>

            <button form="photo-edit-form" class="btn">Ulo??it</button>
        </div>
    </div>
</section>

<?php $this->registerJs(
    <<<JS
    $(document).on('focus', '#photo-latitude, #photo-longitude', function(){
        $('body').removeClass('modal-open');
        $('body').addClass('modal-minimalized');
        
        $('.modal-close-btn').hide();
        $('.modal-up-btn').show();
    });

    $(document).on('click', '.modal-up-btn', function(){
        $('body').removeClass('modal-minimalized');
        $('body').addClass('modal-open');
        
        $('.modal-up-btn').hide();
        $('.modal-close-btn').show();
    });
    
    $('.modal').on('hidden.bs.modal', function () {
        if (typeof actualMarker !== 'undefined'){
            actualMarker.setMap(null);   
        }
    });
JS
);
