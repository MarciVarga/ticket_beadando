<?php

namespace frontend\models;

use Yii;
use yii\base\Model;
use common\models\User;
use yii\db\Query;

/**
 * Class UserForm
 * @package frontend\models
 */
class UserForm extends Model
{
    public $username;
    public $email;
    public $password_hash;
    public $old_password;
    public $user_id;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            ['username', 'trim'],
            ['username', 'required'],
            ['username', 'unique', 'targetClass'=>User::class, 'filter'=>function($query) {
                /** @var Query $query */
                $query->andWhere(['not', ['id'=>$this->user_id]]);
            }],
            ['username', 'string', 'max' => 16],

            ['email', 'trim'],
            ['email', 'required'],
            ['email', 'string', 'max' => 46],

            ['password_hash', 'trim'],
            ['password_hash', 'required'],
            ['password_hash', 'string', 'max' => 36],

            ['old_password', 'trim'],
            ['old_password', 'required'],
            ['old_password', 'string', 'max' => 36],
        ];
    }

    public function attributeLabels() {
        return [
            'password_hash' => 'New Password',
        ];
    }

    /**
     * Updates the user's username, email and password
     *
     * @return bool|null
     */
    public function updateUser()
    {

        if (!$this->validate()) {
            return null;
        }

        $user = User::findOne(Yii::$app->user->identity->getId());

        if ($user->validatePassword($this->old_password)) {
            $user->username = $this->username;
            $user->email = $this->email;
            $user->setPassword($this->password_hash);

            return $user->save();
        } else {
            return false;
        }
    }

    /**
     * @param User $user
     * @return $this
     */
    public function fillFrom(User $user): UserForm
    {
        $this->username = $user->username;
        $this->email = $user->username;
        $this->user_id = $user->id;

        return $this;
    }

    /**
     * @param User $user
     * @return User
     */
    public function fillTo(User $user): User
    {
        $user->username = $this->username;
        $user->email = $this->email;
        $user->setPassword($this->password_hash);

        return $user;
    }

}