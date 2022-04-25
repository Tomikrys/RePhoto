<?php

/* @var $this yii\web\View */

$this->title = 'Profil';
$this->h1 = $this->title;
$this->mainTabsView = '@frontend/views/user/partial/mainTabs';
$this->bodyClasses[] = 'profile';
?>

<section class="container">
    <div class="row">
        <div class="col-md-3">
            <div class="white-box">
                <div class="avatar">
                    <i class="material-icons" style="font-size: 100px;">account_box</i>
                </div>

                <p class="name"><?= Yii::$app->user->identity->name ?></p>
                <p class="email"><?= Yii::$app->user->identity->email ?></p>

                <hr>

                <ul class="user-navigation">
                    <li><a href="<?= \yii\helpers\Url::to(['profile']) ?>"><?= Yii::t('app/user', 'Personal info')?></a></li>
                    <li><a href="<?= \yii\helpers\Url::to(['uploaded-photos']) ?>"><?= Yii::t('app/user', 'Uploaded photos')?></a></li>
                    <li><a href="<?= \yii\helpers\Url::to('/photo/unpublished') ?>"><?= Yii::t('app/user', 'Unpublished photos')?></a></li>
                    <li><a href="<?= \yii\helpers\Url::to('/photo/unaligned') ?>"><?= Yii::t('app/user', 'Unaligned photos')?></a></li>
                </ul>

                <hr>

                <ul class="user-navigation">
                    <li>
                        <?php $form = \yii\bootstrap\ActiveForm::begin([
                            'id' => 'logout-form',
                            'action' => \yii\helpers\Url::to(['logout']),
                        ]); ?>

                        <button><?= Yii::t('app/user', 'Logout')?></button>

                        <?php \yii\bootstrap\ActiveForm::end(); ?>
                    </li>
                </ul>

            </div>
        </div>

        <div class="col-md-9">
            <div class="white-box">
                <h2><?= Yii::t('app/user', 'Uploaded photos')?></h2>
            </div>

            <div class="text-center">
                <?= \yii\widgets\ListView::widget([
                    'id' => 'photos',
                    'dataProvider' => $dataProvider,
                    'itemView' => 'partial/photo',
                    'itemOptions' => ['class' => 'photo'],
                ]); ?>
            </div>

        </div>
    </div>
</section>
