<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class MapAsset extends AssetBundle
{
    public $sourcePath = "@frontend/web/source_assets";

    public $css = [
    ];
    public $js = [
        'js/map.js',
    ];
    public $depends = [
        'frontend\assets\AppAsset',
    ];
}