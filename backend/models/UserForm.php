<?php

namespace backend\models;

use yii\base\Model;
use common\models\User;
use yii\db\Query;

/**
 * Class UserForm
 * @package backend\models
 */
class UserForm extends Model
{
    public $username;
    public $email;
    public $is_admin;
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

            ['is_admin', 'required'],
        ];
    }

    public function attributeLabels() {
        return [
            'is_admin' => 'Admin',
        ];
    }

    /**
     * @param $id
     * @return bool|null
     */
    public function updateUser($id) {

        if (!$this->validate()) {
            return null;
        }

        $user = User::findOne($id);

        $user->username = $this->username;
        $user->email = $this->email;
        $user->is_admin = $this->is_admin;

        return $user->save();
    }

    /**
     * @param User $user
     * @return $this
     */
    public function fillFrom(User $user): UserForm
    {
        $this->username = $user->username;
        $this->email = $user->email;
        $this->is_admin = $user->is_admin;
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
        $user->is_admin = $this->is_admin;

        return $user;
    }

}