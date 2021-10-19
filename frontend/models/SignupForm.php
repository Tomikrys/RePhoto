<?php

namespace frontend\models;

use himiklab\yii2\recaptcha\ReCaptchaValidator;
use yii\helpers\ArrayHelper;

/**
 * Signup form
 */
class SignupForm extends \common\models\SignupForm
{
    public $reCaptcha;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return ArrayHelper::merge(parent::rules(), [
            [['reCaptcha'], ReCaptchaValidator::className()],
        ]);
    }
}
