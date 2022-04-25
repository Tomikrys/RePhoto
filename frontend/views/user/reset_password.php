<?php
/* @var $this \frontend\components\FrontendView */


$this->title = 'Request password reset';
$this->h1 = "Request password reset";
$this->bodyClasses[] = 'login';
$this->showHeader = false;
?>

<section class="valign-wrapper login">
    <div id="login-wrapper">
        <a data-pjax="0" class="valign-wrapper back-btn" href="<?= Yii::$app->request->referrer ?? '/' ?>">
            <i class="material-icons">arrow_back</i>
            <?= Yii::t('app/user', 'Back to page') ?></a>
        <div class="valign-wrapper welcome-box">
            <div>
                <p class="welcome"><?= Yii::t('app/user', 'Welcome to RePhoto') ?></p>
                <img src="/img/logo.png" alt="rephoto">
                <p class="text"><?= Yii::t('app/user', 'Take a look how places has changed in time.') ?></p>
            </div>
        </div>

        <div class="valign-wrapper login-form">
            <div>
                <h2 class="green-font"><?= Yii::t('app/user', 'Set new password') ?></h2>

                <?php $form = \yii\bootstrap\ActiveForm::begin([
                    'id' => 'form-request-password-reset',
                    'options' => [
                        'class' => 'col s12',
                    ]
                ]); ?>

                <?= $form->field($model, 'password', [
                    'options' => [
                        'class' => 'input-field',
                    ],
                ])->passwordInput(['autofocus' => true]) ?>

                <div class="row text-center valign-wrapper">
                    <div class="col s12 no-padding text-center">
                        <button class="btn waves-effect waves-light" type="submit" name="action" data-form="form-request-password-reset">
                            <?= Yii::t('app/user', 'Save') ?>
                            <i class="material-icons right">send</i>
                        </button>
                    </div>
                </div>

                <?php \yii\bootstrap\ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</section>