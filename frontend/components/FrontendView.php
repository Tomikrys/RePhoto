<?php

namespace frontend\components;

use yii\helpers\Html;
use yii\web\View;

/**
 * Frontend controller
 */
class FrontendView extends View
{
    public $bodyClasses = [];
    public $h1;
    public $mainTabsView;
    public $showHeader = true;

    public function head()
    {
        $this->registerJsVar('APP_LANG', \Yii::$app->language);

        parent::head();
    }

    public function endBody()
    {
        parent::endBody();

        $bodyClasses = implode(' ', $this->bodyClasses);
        if ($bodyClasses != '') {
            echo Html::script(<<<JS
            $(document).ready(function() {
                $("body").attr('class', '{$bodyClasses}');
            });
JS
            );
        }
    }
}
