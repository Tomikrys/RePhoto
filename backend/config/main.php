<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-backend',
    'name' => 'RePhoto Admin',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    'language' => 'en',
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
            'baseUrl' => '/admin',
        ],
        'user' => [
            'identityClass' => 'common\models\User',
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-admin', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'app-backend',
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

        'urlManager' => [
            'class' => 'codemix\localeurls\UrlManager',
            // List all supported languages here
            'enableDefaultLanguageUrlCode' => false,
            'languages' => ['en', 'cs'],
            'baseUrl' => '/admin',
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'enableStrictParsing' => false,
            'rules' => [
                '' => 'site/index',
                '/' => 'site/index',
                '/login' => 'site/login',
                '/logout' => 'site/logout',

                '/users' => 'user/index',
                '/users/create' => 'user/create',
                '/users/<id:\d+>' => 'user/view',
                '/users/<id:\d+>/update' => 'user/update',
                '/users/<id:\d+>/delete' => 'user/delete',

                '/places' => 'place/index',
                '/places/create' => 'place/create',
                '/places/<id:\d+>' => 'place/view',
                '/places/<id:\d+>/update' => 'place/update',
                '/places/<id:\d+>/delete' => 'place/delete',


                '/photos/create' => 'photo/create',
                '/photos/<id:\d+>' => 'photo/view',
                '/photos/<id:\d+>/update' => 'photo/update',
                '/photos/<id:\d+>/delete' => 'photo/delete',
            ],
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
                        'app/user' => 'user.php',
                        'app/place' => 'place.php',
                        'app/photo' => 'photo.php',
                    ],
                ],
            ],
        ],
        'assetManager' => [
            'class' => 'common\components\AssetManager',
            'forceCopy' => false,
            'appendTimestamp' => true,
            'linkAssets' => false,
            'converter' => [
                'class' => 'singrana\assets\Converter',
            ],
            'bundles' => [
                'dmstr\web\AdminLteAsset' => [
                    'skin' => 'skin-black',
                ],
            ],
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
    ],
    'params' => $params,
];
