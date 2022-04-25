<?php
/* @var $this \frontend\components\FrontendView */

$this->title = 'Register';
$this->h1 = "Register";
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
                <p class="welcome">Welcome to RePhoto</p>
                <img src="/img/logo.png" alt="rephoto">
                <p class="text"><?= Yii::t('app/user', 'Take a look how places has changed in time.') ?></p>
            </div>
        </div>

        <div class="login-form">
            <h2 class="green-font"><?= Yii::t('app/user', 'Register to RePhoto') ?></h2>

            <?php $form = \yii\bootstrap\ActiveForm::begin([
                'id' => 'form-register',
                'options' => [
                    'class' => 'col s12',
                ]
            ]); ?>


            <div class="row">
                <?= $form->field($model, 'first_name', [
                    'options' => [
                        'class' => 'input-field col s6',
                    ]
                ])->textInput(['autofocus' => true]) ?>

                <?= $form->field($model, 'last_name', [
                    'options' => [
                        'class' => 'input-field col s6',
                    ]
                ]) ?>
            </div>

            <div class="row">
                <?= $form->field($model, 'email', [
                    'options' => [
                        'class' => 'input-field col s12',
                    ]
                ]) ?>
            </div>

            <div class="row">
                <?= $form->field($model, 'password', [
                    'options' => [
                        'class' => 'input-field col s12',
                    ]
                ])->passwordInput() ?>
            </div>

            <div class="row">
                <?= $form->field($model, 'reCaptcha')->widget(\himiklab\yii2\recaptcha\ReCaptcha::className(), [])->label(false) ?>
            </div>

            <div class="row valign-wrapper">
                <div class="col s8 no-padding text-left">
                    <a href="<?= \yii\helpers\Url::to(['/user/login']) ?>"><?= Yii::t('app/user', 'Back to login') ?></a>
                </div>

                <div class="col s4 no-padding text-right">
                    <button class="btn waves-effect waves-light" type="submit" name="action" data-form="form-register">
                        <?= Yii::t('app/user', 'Register') ?>
                        <i class="material-icons right">send</i>
                    </button>
                </div>
            </div>

            <?php \yii\bootstrap\ActiveForm::end(); ?>
        </div>
    </div>
</section>