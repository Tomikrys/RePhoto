<?php
/* @var $this \frontend\components\FrontendView */

use yii\helpers\Url;

$this->title = 'Login';
$this->h1 = "Login";
$this->bodyClasses[] = 'login';
$this->showHeader = false;
?>

<section class="valign-wrapper login">
    <div id="login-wrapper">
        <a data-pjax="0" class="valign-wrapper back-btn" href="<?= Yii::$app->request->referrer ?? '/' ?>">
            <i class="material-icons">arrow_back</i>
            Back to page</a>
        <div class="valign-wrapper welcome-box">
            <div>
                <p class="welcome">Welcome to</p>
                <img src="/img/logo.png" alt="rephoto">
                <p class="text">Take a look how places has changed in time.</p>
            </div>
        </div>

        <div class="login-form">
            <h2 class="green-font">Login to RePhoto</h2>

            <div class="social-login">
                <?= '<a class="facebook-login-btn" href="' . \common\models\User::getFacebookLoginUrl() . '">Log in with Facebook!</a>'; ?>
            </div>

            <p class="green-font">or</p>


            <?php $form = \yii\bootstrap\ActiveForm::begin([
                'id' => 'form-login',
                'options' => [
                    'class' => 'col s12',
                ]
            ]); ?>

            <?= $form->field($loginForm, 'email', [
                'options' => [
                    'class' => 'input-field',
                ],
            ])->textInput(['autofocus' => true]) ?>

            <?= $form->field($loginForm, 'password', [
                'options' => [
                    'class' => 'input-field',
                ],
            ])->passwordInput() ?>

            <div class="row valign-wrapper">
                <div class="col s8 no-padding text-left">
                    <a href="<?= Url::to(['/user/request-password-reset']) ?>">Forgot password?</a>
                </div>

                <div class="col s4 no-padding text-right">
                    <button class="btn waves-effect waves-light" type="submit" name="action" data-form="form-login">Login
                        <i class="material-icons right">send</i>
                    </button>
                </div>
            </div>

            <div class="row text-center">
                <div class="col s12">
                    If you are a new user, <a href="<?= Url::to(['/user/signup']) ?>">signup here.</a>
                </div>

            </div>

            <?php \yii\bootstrap\ActiveForm::end(); ?>
        </div>
    </div>
</section>