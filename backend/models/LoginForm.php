<?php

namespace backend\models;

use common\models\User;

/**
 * Login form
 */
class LoginForm extends \common\models\LoginForm
{
    /**
     * Finds admin by [[email]]
     *
     * @return User|null
     */
    protected function getUser()
    {
        if ($this->_user === null) {
            $this->_user = User::findOne(['email' => $this->email, 'role' => User::ROLE_ADMIN]);
        }

        return $this->_user;
    }
}
