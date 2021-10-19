<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model common\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form box box-primary">

    <?php $form = ActiveForm::begin(); ?>
    <div class="box-body table-responsive">


        <div class="row">
            <?= $form->field($model, 'first_name', [
                'options' => [
                    'class' => 'input-field col-sm-6',
                ]
            ])->textInput(['autofocus' => true]) ?>

            <?= $form->field($model, 'last_name', [
                'options' => [
                    'class' => 'input-field col-sm-6',
                ]
            ]) ?>
        </div>

        <div class="row">
            <?= $form->field($model, 'email', [
                'options' => [
                    'class' => 'input-field col-sm-12',
                ]
            ]) ?>
        </div>

        <div class="row">
            <?= $form->field($model, 'password', [
                'options' => [
                    'class' => 'input-field col-sm-12',
                ]
            ])->passwordInput() ?>
        </div>

    </div>

    <div class="box-footer">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success btn-flat']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
