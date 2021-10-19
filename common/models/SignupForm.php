<?php

namespace common\models;

use yii\base\Model;

/**
 * Signup form
 */
class SignupForm extends Model
{
    const SCENARIO_CREATE = 1;
    const SCENARIO_UPDATE = 2;

    public $first_name;
    public $last_name;
    public $email;
    public $password;

    protected $_user;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['first_name', 'last_name'], 'string', 'max' => 255],
            [['first_name', 'last_name'], 'required'],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'email'],
            ['email', 'string', 'max' => 255],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Emailová adresa je již používaná.', 'on' => static::SCENARIO_CREATE],
            ['email', 'unique', 'targetClass' => '\common\models\User', 'message' => 'Emailová adresa je již používaná.', 'on' => static::SCENARIO_UPDATE, 'when' => function () {
                return User::find()->andWhere(['and', ['!= ', 'id', $this->user->id], ['email' => $this->email]])->exists();
            }],

            ['password', 'required', 'on' => static::SCENARIO_CREATE],
            ['password', 'string', 'min' => 6, 'on' => static::SCENARIO_CREATE],
        ];
    }

    public function attributeLabels()
    {
        return [
            'first_name' => 'Jméno',
            'last_name' => 'Příjmení',
            'email' => 'Email',
            'password' => 'Heslo',
        ];
    }

    /**
     * Signs user up.
     *
     * @return User|null the saved model or null if saving fails
     */
    public function signup()
    {
        if (!$this->validate()) {
            return null;
        }

        $user = new User();
        $user->first_name = $this->first_name;
        $user->last_name = $this->last_name;
        $user->email = $this->email;
        $user->setPassword($this->password);
        $user->access_token = \Yii::$app->security->generateRandomString();
        $user->generateAuthKey();

        return $user->save() ? $user : null;
    }

    public function update()
    {
        if (!$this->validate()) {
            return null;
        }


        $this->_user->setAttributes($this->attributes);

        if ($this->password) {
            $this->user->setPassword($this->password);
            $this->user->generateAuthKey();
        }

        return $this->user->save() ? $this->user : null;
    }

    public function getUser()
    {
        return $this->_user;
    }

    public function setUser(User $user)
    {
        $this->_user = $user;
        $this->setAttributes($user->attributes);
    }
}
