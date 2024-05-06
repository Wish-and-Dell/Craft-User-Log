<?php
namespace wishanddell\userlog\models;

use yii\db\ActiveRecord;

class UserLog extends ActiveRecord
{
    public $username;
    public $email;
    
    /**
     * @return string the name of the table associated with this ActiveRecord class.
     */
    public static function tableName()
    {
        return '{{user_log}}';
    }
}