<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class PhotoAlignerAsset extends AssetBundle
{
    public $sourcePath = "@frontend/web/source_assets";

    public $css = [
    ];
    public $js = [
        'js/photo-aligner.js',
    ];
    public $depends = [
        'frontend\assets\AppAsset',
    ];
}