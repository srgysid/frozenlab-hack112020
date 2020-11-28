<?php
namespace common\helpers;

use Yii;

class MailHelper
{

    public static function notificationUser($model, $user)
    {
        if (isset($user)) {
            $to = [];
            $to[$user['email']] = $user->fullName;
            self::sendNotification($model, 'ppu_notification.php', 'Вид поручения '.$model->typeMessage->typeOrder->name, $to);
        }
    }

    protected static function sendNotification($model, $fileName, $subject, $to)
    {
        Yii::$app->mailer->compose($fileName,[
            'model' => $model,
        ])
            ->setSubject(Yii::t('app', $subject))
            ->setFrom(self::getFrom())
            ->setTo($to)
            ->send();
    }

    protected static function getFrom()
    {
        return ['HotLine@ra.ru' => 'Операторы Горячей линии ДИТ'];
    }

}