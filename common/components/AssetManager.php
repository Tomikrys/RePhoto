<?php

namespace common\components;

use Yii;

class AssetManager extends \yii\web\AssetManager
{
    public function init()
    {
        parent::init();

        if(YII_ENV != 'prod') {
            $this->hashCallback = function ($path) {
                $mostRecentFileMTime = 0;
                $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path), \RecursiveIteratorIterator::CHILD_FIRST);
                foreach ($iterator as $fileinfo) {
                    if ($fileinfo->isFile() && (($fmtime = $fileinfo->getMTime()) > $mostRecentFileMTime)) {
                        $mostRecentFileMTime = $fmtime;
                    }
                }
                $path = (is_file($path) ? dirname($path) : $path) . $mostRecentFileMTime;
                return sprintf('%x', crc32($path . Yii::getVersion()));
            };
        }
    }
}
