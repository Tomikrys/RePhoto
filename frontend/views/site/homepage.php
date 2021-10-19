<?php

/* @var $this \frontend\components\FrontendView */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

$this->title = 'RePhoto';
$this->params['breadcrumbs'][] = $this->title;
$this->bodyClasses[] = 'homepage';


?>

<section id="rephoto">
    <div class="info">
        <h2><?= Yii::t('app/homepage', 'Explore places') ?></h2>
        <p><?= Yii::t('app/homepage', 'and see how the world has changed over time') ?></p>
    </div>

    <div class="overlay"></div>

    <div class="image-wrapper">
        <img src="/img/hp_src.png">
    </div>

    <div class="image-wrapper animated">
        <img src="/img/hp_dst.png">
    </div>

    <div class="input-wrapper">
        <form action="<?= \yii\helpers\Url::to(['/map']) ?>">
            <input autofocus placeholder="<?= Yii::t('app/homepage', 'Type city, street or zipcode') ?>" name="q">
            <button class="btn btn-primary"><?= Yii::t('app/homepage', 'Search') ?></button>
        </form>
    </div>

    <a href="<?= \yii\helpers\Url::to(['/map']) ?>" class="btn btn-primary"><?= Yii::t('app/homepage', 'Explore') ?></a>
</section>

<section id="place-section" class="container">
    <?= $this->render('../map/partial/places', ['places' => $places, 'lastFetch' => true]); ?>
</section>

<section id="register-section">
    <div class="container register-wrapper">
        <?php if (!Yii::$app->user->isGuest): ?>
            <h2><?= Yii::t('app/homepage', 'Go to profile') ?></h2>
            <p><?= Yii::t('app/homepage', 'and find out which places you liked') ?></p>

            <a href="<?= yii\helpers\Url::to(['/user/profile']) ?>" data-pjax="0"
               class="btn btn-primary"><?= Yii::t('app/homepage', 'Open') ?></a>
        <?php else: ?>
            <h2><?= Yii::t('app/homepage', 'Create account') ?></h2>
            <p><?= Yii::t('app/homepage', 'and get access to all functions') ?></p>

            <a href="<?= yii\helpers\Url::to(['/user/signup']) ?>" data-pjax="0"
               class="btn btn-primary"><?= Yii::t('app/homepage', 'Register') ?></a>
        <?php endif; ?>
    </div>
</section>