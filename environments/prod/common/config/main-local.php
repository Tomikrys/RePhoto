<?php
return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'mysql:host=f80b6byii2vwv8cx.chr7pe7iynqr.eu-west-1.rds.amazonaws.com;dbname=gclkx3be7ffav7k3',
            'username' => 'vwc55tk2iqg7oi4z',
            'password' => 'nwpenfr839ewr5i3',
            'charset' => 'utf8',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'viewPath' => '@common/mail',
        ],
    ],
];
