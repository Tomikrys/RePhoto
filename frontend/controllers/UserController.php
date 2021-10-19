<?php

namespace frontend\controllers;

use common\models\LoginForm;
use common\models\User;
use Facebook\Exceptions\FacebookResponseException;
use Facebook\Exceptions\FacebookSDKException;
use Facebook\Facebook;
use frontend\components\FrontendController;
use frontend\models\PasswordResetRequestForm;
use frontend\models\ResetPasswordForm;
use frontend\models\search\PhotoSearch;
use frontend\models\SignupForm;
use Yii;
use yii\base\InvalidArgumentException;
use yii\base\InvalidParamException;
use yii\filters\AccessControl;
use yii\filters\VerbFilter;
use yii\helpers\Url;
use yii\web\BadRequestHttpException;
use yii\web\Response;

/**
 * Class UserController
 * @package frontend\controllers
 */
class UserController extends FrontendController
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout', 'signup', 'profile', 'login'],
                'rules' => [
                    [
                        'actions' => ['signup', 'login'],
                        'allow' => true,
                        'roles' => ['?'],
                    ],
                    [
                        'actions' => ['logout', 'profile', 'uploaded-photos'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * Logs in user.
     * @return string|Response html content or redirection
     */
    public function actionLogin()
    {
        # check if user is already logged
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        # login
        $loginForm = new LoginForm();
        if ($loginForm->load(Yii::$app->request->post()) && $loginForm->login()) {
            # redirect to previous page
            return $this->redirect(\Yii::$app->session->get('login-referrer', '/'));
        }

        # save referrer to session for further redirection
        \Yii::$app->session->set('login-referrer', \Yii::$app->request->referrer);

        return $this->render('login', [
            'loginForm' => $loginForm,
        ]);
    }

    /**
     * Logs out the current user.
     *
     * @return Response redirection
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Signs up user.
     *
     * @return mixed
     */
    public function actionSignup()
    {
        # check if user is already logged
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        # login
        $model = new SignupForm();
        if ($model->load(Yii::$app->request->post())) {
            if ($user = $model->signup()) {
                # login
                Yii::$app->user->login($user);

                # redirect to previous page
                return $this->redirect(\Yii::$app->session->get('sign-up-referrer', '/'));
            }
        }

        # save referrer to session for further redirection
        \Yii::$app->session->set('login-referrer', \Yii::$app->request->referrer);

        return $this->render('register', [
            'model' => $model,
        ]);
    }

    /**
     * Requests password reset.
     *
     * @return mixed
     */
    public function actionRequestPasswordReset()
    {
        $model = new PasswordResetRequestForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            if ($model->sendEmail()) {
                Yii::$app->session->setFlash('success', 'Check your email for further instructions.');

                return $this->redirect(Url::to(['/user/login']));
            } else {
                Yii::$app->session->setFlash('error', 'Sorry, we are unable to reset password for the provided email address.');
            }
        }

        return $this->render('request_password_reset', [
            'model' => $model,
        ]);
    }

    /**
     * Resets password.
     *
     * @param string $token
     * @return mixed
     * @throws BadRequestHttpException
     */
    public function actionResetPassword($token)
    {
        try {
            $model = new ResetPasswordForm($token);
        } catch (InvalidArgumentException $e) {
            throw new BadRequestHttpException($e->getMessage());
        }

        if ($model->load(Yii::$app->request->post()) && $model->validate() && $model->resetPassword()) {
            Yii::$app->session->setFlash('success', 'New password saved.');

            return $this->redirect(Url::to(['/user/login']));
        }

        return $this->render('reset_password', [
            'model' => $model,
        ]);
    }

    /**
     * Displays user profile.
     * @return string html content
     */
    public function actionProfile()
    {
        $user = Yii::$app->user->identity;

        if ($user->load(Yii::$app->request->post())) {
            if ($user->password != "") {
                $user->password_hash = Yii::$app->security->generatePasswordHash($user->password);
            }

            if ($user->save()) {
                Yii::$app->session->setFlash('success', 'Profil úspěšně upraven.');
                $user->password = "";
            }
        }

        return $this->render('profile', [
            'user' => $user,
        ]);
    }

    /**
     * Confirms cookie agreement for logged users.
     * @return array
     */
    public function actionCookieBarClosed()
    {
        $cookies = Yii::$app->response->cookies;

        if (Yii::$app->user->isGuest) {
            # save to cookie
            $cookies->add(new \yii\web\Cookie([
                'name' => 'cookie-bar-closed',
                'value' => true,
            ]));
        } else {
            # save to database
            $user = Yii::$app->user->identity;
            $user->cookie_confirmed = true;

            if (!$user->save()) {
                return ['status' => false];
            }
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        return ['status' => true];
    }


    public function actionLoginFacebook($code, $state)
    {
        $fb = new Facebook([
            'app_id' => '152685308829098', // Replace {app-id} with your app id
            'app_secret' => '921a1001a42fde928d8be54055a6c283',
            'default_graph_version' => 'v2.11',
        ]);

        $helper = $fb->getRedirectLoginHelper();

        try {
            $accessToken = $helper->getAccessToken();
        } catch (FacebookResponseException $e) {
            // When Graph returns an error
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (FacebookSDKException $e) {
            // When validation fails or other local issues
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        if (!isset($accessToken)) {
            if ($helper->getError()) {
                header('HTTP/1.0 401 Unauthorized');
                echo "Error: " . $helper->getError() . "\n";
                echo "Error Code: " . $helper->getErrorCode() . "\n";
                echo "Error Reason: " . $helper->getErrorReason() . "\n";
                echo "Error Description: " . $helper->getErrorDescription() . "\n";
            } else {
                header('HTTP/1.0 400 Bad Request');
                echo 'Bad request';
            }
            exit;
        }

        $oAuth2Client = $fb->getOAuth2Client();

        $tokenMetadata = $oAuth2Client->debugToken($accessToken);
        $tokenMetadata->validateAppId('152685308829098');
        $tokenMetadata->validateExpiration();

        if (!$accessToken->isLongLived()) {
            // Exchanges a short-lived access token for a long-lived one
            try {
                $accessToken = $oAuth2Client->getLongLivedAccessToken($accessToken);
            } catch (FacebookSDKException $e) {
                echo "<p>Error getting long-lived access token: " . $helper->getMessage() . "</p>\n\n";
                exit;
            }
        }

        // get user info
        try {
            // Returns a `Facebook\FacebookResponse` object
            $response = $fb->get('/me?fields=id,first_name,last_name,email', $accessToken->getValue());
        } catch (FacebookResponseException $e) {
            echo 'Graph returned an error: ' . $e->getMessage();
            exit;
        } catch (FacebookSDKException $e) {
            echo 'Facebook SDK returned an error: ' . $e->getMessage();
            exit;
        }

        $fbUser = $response->getGraphUser();
        $tokenMetadata->validateUserId($fbUser->getId());

        $user = User::findOne(['fb_id' => $tokenMetadata->getUserId()])
            ?? User::findOne(['email' => $fbUser->getEmail()])
            ?? new User();

        $user->setAttributes([
            'fb_id' => $tokenMetadata->getUserId(),
            'first_name' => $fbUser->getFirstName(),
            'last_name' => $fbUser->getLastName(),
            'email' => $fbUser->getEmail(),
            'status' => User::STATUS_ACTIVE,
        ]);

        $user->generateAuthKey();

        if ($user->isNewRecord) {
            $user->setPassword(\Yii::$app->security->generateRandomString());
        }

        if (!$user->save()) {
            // TODO facebook login - save error
        }

        Yii::$app->user->login($user, 3600 * 24 * 30);

        return $this->goHome();
    }

    public function actionUploadedPhotos(){
        $photoSearch = new PhotoSearch();
        $dataProvider = $photoSearch->searchUploaded(Yii::$app->user->id, Yii::$app->request->queryParams);

        return $this->render('uploaded_photos', [
            'dataProvider' => $dataProvider,
        ]);
    }
}