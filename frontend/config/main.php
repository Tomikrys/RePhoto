<?php

use himiklab\sitemap\behaviors\SitemapBehavior;

$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-frontend',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log', 'assetsAutoCompress'],
    'controllerNamespace' => 'frontend\controllers',
    'defaultRoute' => 'site/homepage',
    'language' => 'en',
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-frontend',
            'baseUrl' => '',
        ],
        'response' => [
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                /*        Yii::$app->response->headers->add('strict-transport-security', 'max-age=2592000');
                        Yii::$app->response->headers->add('x-frame-options', 'SAMEORIGIN');
                        Yii::$app->response->headers->add('X-Content-Type-Options', 'nosniff');
                        Yii::$app->response->headers->add('X-XSS-Protection', '1; mode=block');
                        Yii::$app->response->headers->add('Content-Security-policy', '');
                        Yii::$app->response->headers->add('Referrer-Policy', 'same-origin'); */
            },
        ],
        'session' => [
//            'cookieParams' => [
//                'httpOnly' => true,
//                'secure' => true
//            ],
            // this is the name of the session cookie used for login on the frontend
            'name' => 'advanced-frontend',
        ],
        'assetManager' => [
            'class' => 'common\components\AssetManager',
            'forceCopy' => false,
            'appendTimestamp' => true,
            'linkAssets' => false,
            'converter' => [
                'class' => 'singrana\assets\Converter',
            ]
        ],
        'assetsAutoCompress' => [
            'class' => '\skeeks\yii2\assetsAuto\AssetsAutoCompressComponent',
            //'enabled' => YII_ENV_PROD,
            'enabled' => false,
            'readFileTimeout' => 3,           //Time in seconds for reading each asset file

            'jsCompress' => true,        //Enable minification js in html code
            'jsCompressFlaggedComments' => true,        //Cut comments during processing js

            'cssCompress' => true,        //Enable minification css in html code

            'cssFileCompile' => true,        //Turning association css files
            'cssFileRemouteCompile' => false,       //Trying to get css files to which the specified path as the remote file, skchat him to her.
            'cssFileCompress' => true,        //Enable compression and processing before being stored in the css file
            'cssFileBottom' => false,       //Moving down the page css files
            'cssFileBottomLoadOnJs' => false,       //Transfer css file down the page and uploading them using js

            'jsFileCompile' => true,        //Turning association js files
            'jsFileRemouteCompile' => false,       //Trying to get a js files to which the specified path as the remote file, skchat him to her.
            'jsFileCompress' => true,        //Enable compression and processing js before saving a file
            'jsFileCompressFlaggedComments' => true,        //Cut comments during processing js

            'htmlCompress' => true,        //Enable compression html
            'noIncludeJsFilesOnPjax' => false,        //Do not connect the js files when all pjax requests
            'htmlCompressOptions' =>              //options for compressing output result
                [
                    'extra' => false,        //use more compact algorithm
                    'no-comments' => true   //cut all the html comments
                ],
        ],
        'urlManager' => [
            'class' => 'codemix\localeurls\UrlManager',
            // List all supported languages here
            'enableDefaultLanguageUrlCode' => false,
            'languages' => ['en', 'cs'],
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'baseUrl' => '/',
            'rules' => [
                ['pattern' => 'sitemap', 'route' => 'sitemap/default/index', 'suffix' => '.xml'],
                
                'map' => 'map/index',
                'editor' => 'editor/index',
                'places/<id:\d+>' => 'place/view',
                'photos/<id:\d+>' => 'photo/edit',
                'places/photo/<id_photo:\d+>/review' => 'place/review-photo',
                'places/photo/<id_photo:\d+>/align' => 'place/align-photo',
                'places/photo/<id_photo:\d+>/confirm' => 'place/confirm-photo',
                'places/<id_place:\d+>/<action>' => 'place/<action>',
            ],
            'ignoreLanguageUrlPatterns' => [
                '#^uploads/#' => '#^uploads#',
            ],
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['/user/login'],
            'identityCookie' => ['name' => '_identity-frontend', 'httpOnly' => true],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'view' => [
            'class' => 'frontend\components\FrontendView',
        ],
        'reCaptcha' => [
            'name' => 'reCaptcha',
            'class' => 'himiklab\yii2\recaptcha\ReCaptcha',

            // Define key in main-local.php
            'siteKey' => '6LfclPgcAAAAANxFsjrXesH0uZBGnG1736YM1xkx',
            'secret' => '6LfclPgcAAAAAF2vTuhjLYWxFVZV-VJv4QfMysZS',
        ],
        'elasticsearch' => [
            'class' => 'yii\elasticsearch\Connection',
            'nodes' => [
                // TODO localhost
                ['http_address' => '127.0.0.1:9200'],
            ],
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/menu' => 'menu.php',
                        'app/homepage' => 'homepage.php',
                        'app/map' => 'map.php',
                        'app/cookie' => 'cookie.php',
                    ],
                ],
            ],
        ],
    ],
    'modules' => [
        'sitemap' => [
            'class' => 'himiklab\sitemap\Sitemap',
            'models' => [
                [
                    'class' => 'common\models\Place',
                    'behaviors' => [
                        'sitemap' => [
                            'class' => SitemapBehavior::className(),
                            'scope' => function ($model) {
                                /** @var \yii\db\ActiveQuery $model */
                                $model->select(['id', 'updated_at']);
                                //$model->andWhere(['visible' => 1]);
                            },
                            'dataClosure' => function ($model) {
                                /** @var self $model */
                                return [
                                    'loc' => \yii\helpers\Url::to(['/place/view', 'id' => $model->id], true),
                                    'lastmod' => $model->updated_at,
                                    'changefreq' => SitemapBehavior::CHANGEFREQ_MONTHLY,
                                    'priority' => 0.8
                                ];
                            }
                        ],
                    ],
                ],
            ],
            'urls' => [
                // your additional urls
                [
                    'loc' => '/',
                    'changefreq' => SitemapBehavior::CHANGEFREQ_MONTHLY,
                    'priority' => 1,
                ],
                [
                    'loc' => '/map',
                    'changefreq' => SitemapBehavior::CHANGEFREQ_DAILY,
                    'priority' => 0.9,
                ],
                [
                    'loc' => '/user/login',
                    'changefreq' => SitemapBehavior::CHANGEFREQ_MONTHLY,
                    'priority' => 0.6,
                ],
                [
                    'loc' => '/user/signup',
                    'changefreq' => SitemapBehavior::CHANGEFREQ_MONTHLY,
                    'priority' => 0.6,
                ],
                [
                    'loc' => '/user/request-password-reset',
                    'changefreq' => SitemapBehavior::CHANGEFREQ_MONTHLY,
                    'priority' => 0.4,
                ],
                [
                    'loc' => '/user/check-login',
                    'changefreq' => SitemapBehavior::CHANGEFREQ_MONTHLY,
                    'priority' => 0.4,
                ],
                [
                    'loc' => '/editor',
                    'changefreq' => SitemapBehavior::CHANGEFREQ_MONTHLY,
                    'priority' => 0.7,
                ],
                [
                    'loc' => '/takephoto',
                    'changefreq' => SitemapBehavior::CHANGEFREQ_MONTHLY,
                    'priority' => 0.7,
                ],
            ],
            'enableGzip' => true, // default is false
            'cacheExpire' => 1, // 1 second. Default is 24 hours
        ],
    ],
    'params' => $params,
];
