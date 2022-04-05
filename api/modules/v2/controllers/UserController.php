<?php

namespace api\modules\v2\controllers;

use common\models\SignupForm;
use common\models\User;
use frontend\models\PasswordResetRequestForm;
use yii\web\BadRequestHttpException;
use yii\web\HttpException;
use yii\web\MethodNotAllowedHttpException;

class UserController extends \api\components\ApiController
{
    public function actionLogin()
    {
        $post = \Yii::$app->request->post();

        $user = User::find()
            ->andWhere(['email' => $post['email']])
            ->one();

        if (!\Yii::$app->security->validatePassword($post['password'], $user->password_hash)) {
            throw new HttpException(401, 'email or password is incorrect');
        }

        $user->access_token = \Yii::$app->security->generateRandomString();
        if (!$user->save()) {
            throw new BadRequestHttpException(current($user->errors)[0]);
        }

        return $user->getApiStatusData();
    }

    public function actionLogout()
    {
        $auth = explode(' ', \Yii::$app->request->headers->get('authorization'));
        $token = $auth[1] ?? false;
        $user = User::findOne(['access_token' => $token]);
        if (!$user) {
            throw new HttpException(401, 'token is not valid');
        }
        $user->access_token = null;

        if (!$user->save()) {
            throw new BadRequestHttpException(current($user->errors)[0]);
        }

        return [];
    }

    public function actionCheckLogin()
    {
        $auth = explode(' ', \Yii::$app->request->headers->get('authorization'));
        $token = $auth[1] ?? false;
        $user = User::findOne(['access_token' => $token]);
        if (!$user) {
            throw new HttpException(401, 'token is not valid');
        }
        return $user->getApiStatusData();
    }


    public function actionRequestPasswordReset()
    {
        $auth = explode(' ', \Yii::$app->request->headers->get('authorization'));
        $token = $auth[1] ?? false;
        $user = User::findOne(['access_token' => $token]);
        if (!$user) {
            throw new HttpException(401, 'token is not valid');
        }
        $post = \Yii::$app->request->post();
        $model = new PasswordResetRequestForm();
        $model->email = $post['email'];

        if ($model->validate() && $model->sendEmail()) {
            return [];
        }

        throw new BadRequestHttpException(current($model->errors)[0]);
    }

    public function actionIndex()
    {
        switch (\Yii::$app->request->method) {
            case 'POST':
                return $this->create();

            case 'PUT':
                return $this->update();

            case 'GET':
                return $this->getStatus();
            default:
                throw new MethodNotAllowedHttpException();
        }
    }

    public function create()
    {
        $form = new SignupForm();
        $form->scenario = SignupForm::SCENARIO_CREATE;
        $post = \Yii::$app->request->post();
        $form->setAttributes($post);
        $user = $form->signup();

        if (!$user) {
            throw new BadRequestHttpException(current($form->errors)[0]);
        }

        return $user->getApiStatusData();
    }

    public function update()
    {
        $auth = explode(' ', \Yii::$app->request->headers->get('authorization'));
        $token = $auth[1] ?? false;
        $user = User::findOne(['access_token' => $token]);
        if (!$user) {
            throw new HttpException(401, 'token is not valid');
        }

        $post = \Yii::$app->request->post();
        $user->setAttributes([
            'first_name' => $post['first_name'] ?? $user->first_name,
            'last_name' => $post['last_name'] ?? $user->last_name,
            'email' => $post['email'] ?? $user->email,
        ]);

        if (isset($post['password'])) {
            $user->password_hash = \Yii::$app->security->generatePasswordHash($post['password']);
        }

        if (!$user->save()) {
            throw new BadRequestHttpException(current($user->errors)[0]);
        }

        return $user->getApiStatusData();
    }

    public function getStatus()
    {
        $auth = explode(' ', \Yii::$app->request->headers->get('authorization'));
        $token = $auth[1] ?? false;
        $user = User::findOne(['access_token' => $token]);
        if (!$user) {
            throw new HttpException(401, 'token is not valid');
        }

        return $user->getApiStatusData();
    }
}