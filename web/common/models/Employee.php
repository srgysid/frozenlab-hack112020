<?php
namespace common\models;

use common\helpers\AuthHelper;
use common\models\User;
use common\models\UserProfile;
use Yii;
use yii\base\Exception;
use yii\base\Model;
use yii\db\ActiveQuery;
use yii\db\Expression;
use yii\db\Query;
use yii\helpers\ArrayHelper;
use yii\web\ForbiddenHttpException;
use yii\web\HttpException;
use yii\web\NotFoundHttpException;


class Employee extends Model
{
    const SCENARIO_REGISTER = 'scenario_register';
    const SCENARIO_UPDATE = 'scenario_update';
    const SCENARIO_CHANGE_PASS = 'scenario_change_pass';

    public $id;
    public $first_name;
    public $second_name;
    public $third_name;
    public $phone;
    public $password;
    public $password_repeat;
    public $status;
    public $role;
    public $username;
    public $card_id;
    public $department_id;
    public $department_name;
    public $staffpos_name;
    public $sign_chief;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
//            [['first_name', 'second_name'], 'required', 'on' => [self::SCENARIO_REGISTER, self::SCENARIO_UPDATE]],
            [['first_name', 'second_name', 'third_name', 'department_name', 'staffpos_name'], 'string', 'max' => 255],
            [['phone'], 'safe'],
            [['phone'], 'match', 'pattern' => '/^\d{10}$/','enableClientValidation'=> false],
            [['phone'], 'validateUserPhone', 'on' => [self::SCENARIO_REGISTER, self::SCENARIO_UPDATE]],
            [['phone'], 'required', 'on' => [self::SCENARIO_REGISTER, self::SCENARIO_UPDATE]],
            [['sign_chief'], 'boolean'],
            [['id'], 'exist', 'skipOnError' => true, 'targetClass' => User::className(), 'targetAttribute' => ['id' => 'id']],
            [['password_repeat', 'password'], 'string', 'min' => 6],
            [['password_repeat', 'password'], 'required', 'on' => [self::SCENARIO_REGISTER, self::SCENARIO_CHANGE_PASS]],
            ['password_repeat', 'validatePasswordRepeat', 'skipOnEmpty' => false, 'skipOnError' => false, 'on' => [self::SCENARIO_REGISTER, self::SCENARIO_CHANGE_PASS]],

            [['role'], 'string', 'max' => 50],
            [['role'], 'in', 'range' => array_keys(AuthHelper::getRoles())],
            [['role'], 'required', 'on' => [self::SCENARIO_REGISTER, self::SCENARIO_UPDATE]],
            [['role'], 'default', 'value' => null],

            [['username'], 'string', 'max' => 50],
            [['username'], 'required', 'on' => [self::SCENARIO_REGISTER, self::SCENARIO_UPDATE]],
            [['username'], 'validateUniqueUsername', 'on' => [self::SCENARIO_REGISTER, self::SCENARIO_UPDATE]],
            [['first_name', 'second_name', 'third_name', 'username'], 'filter', 'filter' => 'trim'],
            [['card_id'], 'required', 'on' => [self::SCENARIO_REGISTER, self::SCENARIO_UPDATE]],
            [['card_id'], 'exist', 'skipOnError' => true, 'targetClass' => Card::className(), 'targetAttribute' => ['card_id' => 'id']],
            [['department_id'], 'integer'],
            [['department_id'], 'exist', 'skipOnError' => true, 'targetClass' => Department::className(), 'targetAttribute' => ['department_id' => 'id']],
        ];
    }

    public function scenarios()
    {
        $scenarios = parent::scenarios();
        $scenarios[self::SCENARIO_REGISTER] = ['phone', 'first_name', 'second_name','third_name','password_repeat', 'password', 'role', 'username', 'card_id', 'department_id', 'sign_chief'];
        $scenarios[self::SCENARIO_UPDATE] = ['phone', 'first_name', 'second_name','third_name','role', 'username', 'card_id', 'department_id', 'sign_chief'];
        $scenarios[self::SCENARIO_CHANGE_PASS] = ['password_repeat', 'password'];
        return $scenarios;
    }

    public function attributeLabels()
    {
        return [
            'phone' => 'Телефон',
            'first_name' => 'Имя',
            'second_name' => 'Фамилия',
            'third_name' => 'Отчество',
            'password' => 'Пароль',
            'password_repeat' => 'Повтор пароля',
            'status' => 'Статус',
            'role' => 'Роль',
            'username' => 'Имя пользователя',
            'card_id' => 'ФИО пользователя',
            'department_id' => 'Подразделение',
            'department_name' => 'Подразделение',
            'staffpos_name' => 'Должность',
            'sign_chief' => 'Признак руководителя',
        ];
    }

    public function validatePasswordRepeat($attribute, $params, $validator)
    {
        if ($this->password != $this->{$attribute}) {
            $this->addError($attribute, Yii::t('app', 'Не верно указан повтор пароля'));
        }
    }

    /**
     * Проверка на уникальность имени пользователя
     * @param $attribute
     * @param $params
     * @param $validator
     */
    public function validateUniqueUsername($attribute, $params, $validator)
    {
        $user = $this->$attribute;
        if ($this->id) {
            $duplicateUser = User::find()
                ->andWhere(['username' => $user])
                ->andWhere(['<>', 'id', $this->id])
                ->one();
        } else {
            $duplicateUser = User::findOne(['username' => $user]);
        }

        if ($duplicateUser) {
            $this->addError($attribute, Yii::t('app', 'Имя пользователя уже задействовано для другого пользователя'));
        }
    }

    public function validateUserPhone($attribute, $params, $validator)
    {
        $phone = $this->{$attribute};
        $user = User::findByPhone($phone);
        if ($user) {
            if ($this->id) {
                if ($this->id != $user->id) {
                    $this->addError($attribute, Yii::t('app', 'Пользователь с таким номером телефона уже зарегистрирован'));
                }
            }
            else $this->addError($attribute, Yii::t('app', 'Пользователь с таким номером телефона уже зарегистрирован'));
        }

    }

    /**
     * Регистрация нового работника
     * @throws \Throwable
     */
    public function register()
    {
//        $session = Yii::$app->session;
//        $company_id = $session['current_company_id'];

        $transaction = Yii::$app->db->beginTransaction();
        try {
            // создаем пользователя
            $user = new User();
            $user->username = $this->username;
            $user->email = self::usernameToEmail($this->username);
            $user->status = User::STATUS_ACTIVE;
            $user->password_hash = Yii::$app->security->generatePasswordHash($this->password);
            $user->generateAuthKey();

            if ($user->save()) {
                $user->refresh();
            } else {
                $this->addErrors($user->errors);
                $transaction->rollBack();
                return false;
            }
            $this->id = $user->id;

            // add role
            $auth = Yii::$app->authManager;
            $role = $auth->getRole($this->role);
            $auth->assign($role, $user->id);

            $modelCard = Card::findOne($this->card_id);
            if ($modelCard){
                $this->first_name = $modelCard->firstname;
                $this->second_name = $modelCard->secondname;
                $this->third_name = $modelCard->thirdname;
            }

            $department = Department::getDepartmentByCardId($this->card_id);
            if ($department) $departmnet_id = $department['department_id'];
            else $departmnet_id = 0;

            // создаем профиль
            $userProfile = new UserProfile([
                'user_id' => $user->id,
                'first_name' => $this->first_name,
                'second_name' => $this->second_name,
                'third_name' => $this->third_name,
                'phone' => $this->phone,
                'card_id' => $this->card_id,
                'department_id' => $departmnet_id,
                'sign_chief' => $this->sign_chief,
            ]);

            if (!$userProfile->save()) {
                $this->addErrors($user->errors);
                $transaction->rollBack();
                return false;
            }

            $transaction->commit();

        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }

        return true;
    }


    public static function findByUserId($user_id)
    {
        $user = User::findOne(['id' => $user_id]);
        if (!$user) return null;
        $profile = $user->userProfile;

        $employee = new Employee([
            'id' => $user->id,
            'first_name' => $profile->first_name,
            'second_name' => $profile->second_name,
            'third_name' => $profile->third_name,
            'phone' => $profile->phone,
            'card_id' => $profile->card_id,
            'department_id' => $profile->department_id,
            'status' => $user->status,
            'role' => self::getEmployeeRole($user->id),
            'username' => $user->username,
            'department_name' => $profile->department->getShortCodeShortName(),
            'staffpos_name' => $profile->card->getDataByCard($profile->card->stabnum)['staffpos_description'],
            'sign_chief' => $profile->sign_chief,
        ]);

        return $employee;
    }


    /**
     * Полное удаление сотруника, вместе с пользователем
     * @throws \Throwable
     */
    public function deleteEmployee()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // remove old pre-registration
            UserProfile::deleteAll(['user_id' => $this->id]);
            User::findOne(['id' => $this->id])->delete();

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function activateUser()
    {
        $user = User::findOne($this->id);
        $user->status = User::STATUS_ACTIVE;
        return $user->save(false);
    }

    public function deactivateUser()
    {
        $user = User::findOne($this->id);
        $user->status = User::STATUS_INACTIVE;
        return $user->save(false);
    }

    public static function getEmployeeRole($user_id)
    {
        $auth = Yii::$app->authManager;
        $roles = $auth->getRolesByUser($user_id);
        $roles = array_keys($roles);
        $roles = array_diff($roles, ['guest']);
        $role = (count($roles) ? array_shift($roles) : null);
        return $role;
    }

    public function updateProfile()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {

            // update user profile
            $user = User::findOne(['id' => $this->id]);
            $user->username = $this->username;
            $user->email = self::usernameToEmail($this->username);
            if (!$user->save(false)) {
                $this->addError('username', 'Ошибка в имени пользователя');
                $transaction->rollBack();
                return false;
            }

            $modelCard = Card::findOne($this->card_id);
            if ($modelCard){
                $this->first_name = $modelCard->firstname;
                $this->second_name = $modelCard->secondname;
                $this->third_name = $modelCard->thirdname;
            }
            $department = Department::getDepartmentByCardId($this->card_id);
            if ($department) $departmnet_id = $department['department_id'];
            else $departmnet_id = 0;

            $userProfile = UserProfile::findOne(['user_id' => $this->id]);
            $userProfile->first_name = $this->first_name;
            $userProfile->second_name = $this->second_name;
            $userProfile->third_name = $this->third_name;
            $userProfile->phone = $this->phone;
            $userProfile->card_id = $this->card_id;
            $userProfile->department_id = $departmnet_id;
            $userProfile->sign_chief = $this->sign_chief;
            if (!$userProfile->save(true)) {
                $this->errors = $userProfile->errors;
                $transaction->rollBack();
                return false;
            }

            // update role
            $auth = Yii::$app->authManager;
            $role = $auth->getRole($this->role);
            // удаляем текущие роли
            $auth->revokeAll($this->id);
            // присваиваем новую роль
            $auth->assign($role, $this->id);

            $transaction->commit();
            return true;
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function getFullName()
    {
        return "{$this->second_name} {$this->first_name} {$this->third_name}";
    }

    public function changePass()
    {
        $user = User::findOne(['id' => $this->id]);
        $user->password_hash = Yii::$app->security->generatePasswordHash($this->password);
        $user->generateAuthKey();
        $user->save(false);
        return true;
    }

    public static function usernameToEmail($username) {
        return trim($username).'@'.Yii::$app->params['username_domain'];
    }

    public static function emailToUsername($email) {
        $parts = explode('@', $email);
        if ($parts) return $parts[0];
        else return null;
    }

}