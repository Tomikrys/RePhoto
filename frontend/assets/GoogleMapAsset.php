<?php

namespace frontend\assets;

use yii\web\AssetBundle;

/**
 * Main frontend application asset bundle.
 */
class GoogleMapAsset extends AssetBundle
{
    public $sourcePath = "@frontend/web/source_assets";

    public $css = [
    ];
    public $js = [
        'https://developers.google.com/maps/documentation/javascript/examples/markerclusterer/markerclusterer.js',
        'https://maps.googleapis.com/maps/api/js?key=AIzaSyAxwWfbKcXiMKNmJLtWpdYtu5ruriw_YS0&callback=initMap&libraries=places',
    ];
    public $depends = [
        'frontend\assets\AppAsset',
    ];
}