<?php
namespace wishanddell\userlog\services;

use yii\base\Component;
use wishanddell\userlog\Plugin;
use wishanddell\userlog\models\UserLog;

class Log extends Component
{
	/**
	 * Save user details to database table on login
	 */
    public function onLogin($user, $ip)
	{
	    $record = new UserLog();
        $record->userId = $user->id;
        $record->ipAddress = $ip;
        $record->location = $this->getLocationFromIp($ip);
        $record->save();
	}
	
	/**
	 * Get the full log data
	 */
	public function getLog($limit = 100, $offset = 0)
	{
		$query = UserLog::find()
			->select('user_log.*, users.email, users.username')
		    ->leftJoin('users', '`users`.`id` = `user_log`.`userId`')
			->limit($limit);
			
		if ($offset)
			$query->offset($offset);
			
		return $query->orderBy('dateCreated DESC');
	}
	
	/**
	 * Get the log data for a specific user
	 */
	public function getLogByUser($userId)
	{
		return UserLog::find()
			->where(['userId' => $userId])
			->orderBy('dateCreated DESC')
			->all();
	}
	
	/**
	 * Rmove specific log ids
	 */
	public function remove($ids)
	{
		UserLog::deleteAll(['in', 'id', $ids]);
	}
	
	/**
	 * Remove entries older than X days
	 */
	public function clean()
	{
		$days = Plugin::getInstance()->getSettings()->days;
		
	    // Subtract days
	    $date = new \DateTime();
        $date->sub(new \DateInterval('P'.$days.'D'));
        
        // Delete all older than
	    UserLog::deleteAll(['<', 'dateCreated', $date->format('Y-m-d H:i:s')]);
	}
	
	/**
	 * Find a users location from their IP address
	 */
	public function getLocationFromIp($ip)
	{
		$settings = Plugin::getInstance()->settings;
		if ($settings->ipService == 'disabled' || !$settings->ipstackKey)
			return 'No Service';
		
	    $feed = file_get_contents('http://api.ipstack.com/'.$ip.'?access_key='.$settings->ipstackKey);
	    if (!$feed)
	    	return 'Failed to Lookup';
	    
	    $data = json_decode($feed);
		if (!isset($data->country_name) || is_null($data->country_name))
			return 'Unavailable';
		
	    return $data->city.', '.$data->country_name;
	}
}