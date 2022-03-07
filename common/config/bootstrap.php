<?php
Yii::setAlias('@root', dirname(dirname(__DIR__)));
Yii::setAlias('@common', dirname(__DIR__));
Yii::setAlias('@api', dirname(dirname(__DIR__)) . '/api');
Yii::setAlias('@frontend', dirname(dirname(__DIR__)) . '/frontend');
Yii::setAlias('@backend', dirname(dirname(__DIR__)) . '/backend');
Yii::setAlias('@console', dirname(dirname(__DIR__)) . '/console');
Yii::setAlias('@uploads', dirname(dirname(__DIR__)) . '/uploads');
Yii::setAlias('@web', dirname(dirname(__DIR__)) . '/frontend/web');

function dieArray($var)
{
    echo "<pre>";
    var_dump($var);
    echo "</pre>";
    die();
}

function stringPriview(string $string, int $maxLength)
{
    $l = mb_strlen($string);
    if ($l > $maxLength){
        $string = mb_substr($string, 0, 33) . " ...";
    }

    return $string;
}