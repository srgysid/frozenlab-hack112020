<?php

use yii\db\Migration;

/**
 * Class m201128_013627_add_user_profile_column
 */
class m201128_013627_add_user_profile_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('user_profile', 'sign_chief', $this->boolean());
        $this->initRole('rl_chief');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->deleteRole('rl_chief');
        $this->dropColumn('user_profile', 'sign_chief');
    }

    public function initRole($roleName)
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($roleName);
        if (!$role) {
            $role = $auth->createRole($roleName);    // create role if not exists yet
            $auth->add($role);
        }
        return $role;
    }

    public function deleteRole($roleName)
    {
        $auth = Yii::$app->authManager;
        $role = $auth->getRole($roleName);
        if ($role) {
            $auth->removeChildren($role);
            $auth->remove($role);
        }
    }

}
