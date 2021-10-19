<?php

/* @var $this yii\web\View */
/* @var $name string */
/* @var $message string */

/* @var $exception Exception */

use yii\helpers\Html;

$this->title = $exception->statusCode;
//$this->h1 = $this->title;
$this->bodyClasses[] = 'error';
?>
<section class="container valign-wrapper error">
    <div class="text-center error-wrapper">
        <h1><?= Html::encode($exception->statusCode) ?></h1>

        <p>
            <?= nl2br(Html::encode($message)) ?>
        </p>

        <a class="btn" href="/">Back to homepage</a>
    </div>

</section>
