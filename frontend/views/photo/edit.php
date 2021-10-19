<?php
/* @var $photo \common\models\Photo */
/* @var $this \yii\web\View */

\kartik\switchinput\SwitchInputAsset::register($this);
?>


<div id="edit-photo-wrapper" class="row">
    <div class="header">
        <h2>Editace informací o fotce</h2>
        <i class="material-icons modal-close-btn">close</i>
        <i class="material-icons modal-up-btn" style="display: none">keyboard_arrow_up</i>
    </div>

    <div class="col s3">
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

    <div class="row">
        <div class="col s12">
            <?= $form->field($photo, 'name') ?>
        </div>
    </div>

    <div class="row">
        <div class="col s6">
            <?= $form->field($photo, 'latitude') ?>
        </div>

        <div class="col s6">
            <?= $form->field($photo, 'longitude') ?>
        </div>
    </div>

    <div class="row">
        <div class="col s12">
            <?= $form->field($photo, 'description')->textarea() ?>
        </div>
    </div>

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
        <a href="<?= \yii\helpers\Url::to('/photo/unpublished') ?>"
           class="btn btn-default open-in-modal">Zpět</a>

        <button form="photo-edit-form"
           class="btn">Uložit</button>
    </div>
</div>

<? $this->registerJs(<<<JS
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