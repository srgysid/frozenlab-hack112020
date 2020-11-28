<?php
namespace common\helpers;

use Yii;

class AuthHelper
{
    const RL_ADMIN = 'rl_admin';
    const RL_KEY_USER = 'rl_key_user';
    const RL_CHIEF = 'rl_chief';
    const RL_VIEW_USER = 'rl_view_user';

    public static function getRoles()
    {
        return [
            'rl_admin' => 'Администратор',
            'rl_key_user' => 'Ключевой пользователь предприятия',
            'rl_chief' => 'Руководитель',
            'rl_view_user' => 'Пользователь',
        ];
    }

    public static function getUserRoles($user_id)
    {
        $roles = Yii::$app->authManager->getRolesByUser($user_id);
        $roles = array_keys($roles);
        $roles = array_filter($roles, function ($val){
            return ($val != 'guest');
        });
        $roles = array_values($roles);

        return $roles;
    }

}