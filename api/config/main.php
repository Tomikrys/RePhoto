<?php
$params = array_merge(
    require(__DIR__ . '/../../common/config/params.php'),
    require(__DIR__ . '/../../common/config/params-local.php'),
    require(__DIR__ . '/params.php'),
    require(__DIR__ . '/params-local.php')
);

return [
    'id' => 'app-api',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'user' => [
            'identityClass' => 'common\models\User',
        ],

        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@app/messages',
                    'sourceLanguage' => 'en-US',
                    'fileMap' => [

                    ],
                ],
            ],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-api',
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
        'request' => [
            'scriptUrl' => '/api',
            'enableCookieValidation' => false,
            'enableCsrfValidation' => false,
            'parsers' => [
                'application/json' => 'yii\web\JsonParser',
            ],
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'normalizer' => [
                'class' => 'yii\web\UrlNormalizer',
            ],
            'rules' => [
                '<version>/places' => '<version>/places/index',
                '<version>/places/<id:\d+>' => '<version>/places/view',
                '<version>/places/<id:\d+>/<action>' => '<version>/places/<action>',
                '<version>/places/<id:\d+>/photo/<id_photo:\d+>' => '<version>/places/photo',
            ],
        ],
        'response' => [
            'format' => \yii\web\Response::FORMAT_JSON,
            'class' => 'yii\web\Response',
            'on beforeSend' => function ($event) {
                # change response only if JSON
                if (\Yii::$app->response->format == \yii\web\Response::FORMAT_JSON || \Yii::$app->response->format == \yii\web\Response::FORMAT_JSONP) {
                    $response = $event->sender;
                    if ($response->data !== null) {
                        $data = null;

                        # Handle exceptions
                        $typeClass = isset($response->data['type']) ? (new ReflectionClass($response->data['type'])) : false;
                        if ($typeClass && ($typeClass->isSubclassOf('Exception') || $typeClass->isSubclassOf('Error'))) {
                            $code = $response->data['code'];
                            $statusCode = $response->status ?? 500;
                            $errorMessage = $response->data['message'];

                            // do not reformat exception in debug mode
                            if (YII_DEBUG) {
                                return;
                            }
                        } else {
                            $statusCode = $response->statusCode;
                            $data = $response->data['message'] ?? $response->data;
                        }

                        $response->data = [];
                        $response->data['status'] = $code ?? $statusCode;

                        if (isset($errorMessage)) {
                            $response->data['error'] = $errorMessage;
                        }

                        if (isset($data)) {
                            $response->data['data'] = $data;
                        }

                        $response->statusCode = $statusCode;
                    }
                }
            },
        ],
    ],
    'modules' => [
        'v1' => [
            'basePath' => '@api/modules/v1',
            'class' => 'api\modules\v1\Module',
        ],
    ],
    'params' => $params,
];
