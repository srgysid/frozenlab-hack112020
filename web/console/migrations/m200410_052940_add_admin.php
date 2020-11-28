<?php

use yii\db\Migration;

/**
 * Class m200410_052940_add_admin
 */
class m200410_052940_add_admin extends Migration
{

//    yii migrate --migrationPath=@yii/rbac/migrations - создание таблиц RBAC до add_admin

    public function safeUp()
    {
        $user['username'] = 'admin';
        $user['email'] = 'admin@ra.ru';
        $user['password_hash'] = \Yii::$app->security->generatePasswordHash('123456');
        $user['auth_key'] = \Yii::$app->security->generateRandomString();
        $user['created_at'] = $user['updated_at'] = time();
        $this->insert('user', $user);

        $user_id = $this->db->getLastInsertID();

        $this->createTable('user_profile', [
            'id' => $this->primaryKey(),
            'user_id' => $this->integer(),
            'first_name' => $this->string(255),
            'second_name' => $this->string(255),
            'third_name' => $this->string(255),
            'phone' => $this->bigInteger()->notNull(),
            'email' => $this->string(64),
        ]);

        $this->addForeignKey("fk_user_profile_user", 'user_profile', "user_id", "user", "id", "RESTRICT", "RESTRICT");
        $this->createIndex('idx_user_profile_user_id', 'user_profile', 'user_id');

        $this->insert('user_profile', [
            'user_id' => $user_id,
            'first_name' => 'Админов',
            'second_name' => 'Админ',
            'third_name' => 'Админович',
            'phone' => 1234567890,

        ]);

        $auth = Yii::$app->authManager;

        $this->initRole('rl_key_user');
        $this->initRole('rl_view_user');

        $adminRole = $this->initRole('rl_admin'); // create admin role
        $auth->assign($adminRole, $user_id);    // asign admin role to user
    }

    public function safeDown()
    {
        $this->deleteRole('rl_admin');
        $this->deleteRole('rl_key_user');
        $this->deleteRole('rl_view_user');

        $this->dropTable('user_profile');
        $this->delete('user');
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

    /**
     * Deletes role
     * @param $roleName string Role name
     * @return null|\yii\rbac\Role Role if found, else create and return permission
     * @throws Exception
     */
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
