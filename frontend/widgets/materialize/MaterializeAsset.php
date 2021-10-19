<?php

namespace frontend\widgets\materialize;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class MaterializeAsset extends AssetBundle
{
    public $sourcePath = '@frontend/widgets/materialize/assets';

    public $css = [
        'css/nouislider.min.css',
        'css/materialize.css',
    ];
    public $js = [
        'js/nouislider.min.js',
        'js/materialize.min.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
    ];
}
