<?php

/* @var $this \frontend\components\FrontendView */

/* @var $content string */

use common\widgets\Alert;
use frontend\assets\AppAsset;
use yii\widgets\Pjax;

AppAsset::register($this);
?>
<?php $this->beginPage() ?>
<!DOCTYPE html>
<html lang="<?= Yii::$app->language ?>">
<head>
    <?= $this->render('partial/head') ?>

    <?php $this->head() ?>
</head>
<body class="<?= implode(' ', $this->bodyClasses) ?>">
<?php $this->beginBody() ?>

<section id="main-content">

    <div id="main-progress" class="progress">
        <div class="indeterminate"></div>
    </div>

    <?php if ($this->showHeader): ?>
        <header>
            <?= $this->render('partial/header'); ?>
        </header>
    <?php endif; ?>

    <main>
        <?php Pjax::begin(['id' => 'main-pjax', 'options' => ['class' => 'pjax-wrapper']]) ?>

        <?= Alert::widget() ?>

        <?= $content ?>

        <?php Pjax::end() ?>
    </main>

    <?= $this->render('partial/cookiebar'); ?>

</section>

<?php $this->endBody() ?>

</body>
</html>
<?php $this->endPage() ?>
