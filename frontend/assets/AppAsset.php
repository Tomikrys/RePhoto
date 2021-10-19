<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class AppAsset extends AssetBundle
{
    public $sourcePath = "@frontend/web/source_assets";

    public $css = [
        'css/jquery-ui.min.css',
        'css/site.less',
        'css/dropzone.less',
    ];
    public $js = [
        'js/jquery-ui.min.js',
        'js/main.js',
        'js/dropzone.js',
        'js/editor.js',
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'frontend\widgets\materialize\MaterializeAsset',
    ];
}
