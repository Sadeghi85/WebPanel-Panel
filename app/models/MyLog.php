<?php

class MyLog extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'logs';
	
	/**
     * One to many relationship.
     *
     * @return Model
     */
    public function user()
    {
		return $this->belongsTo('User');
    }
	
	/**
     * One to many relationship.
     *
     * @return Model
     */
    public function site()
    {
		return $this->belongsTo('Site');
    }
	
	public function scopeNewest($query)
    {
        return $query->orderBy('updated_at', 'desc');
    }
	
	public static function hasAccessToLogs()
	{
		if (Sentry::check() and (Sentry::getUser()->inGroup(Sentry::findGroupByName('Root')) or Sentry::getUser()->hasAccess('log.self') or Sentry::getUser()->hasAccess('log.all') or Sentry::getUser()->hasAccess('log.nonroot')))
		{
			return true;
		}
		
		return false;
	}
	
	public static function insertLog($params)
    {
		$myLog = new MyLog;
		
		$myLog->description = isset($params['description']) ? $params['description'] : '';
		$myLog->user_id = isset($params['user_id']) ? $params['user_id'] : null;
		$myLog->site_id = isset($params['site_id']) ? $params['site_id'] : null;
		$myLog->event = isset($params['event']) ? $params['event'] : null;
		$myLog->type = isset($params['type']) ? $params['type'] : null;
		
		try
		{
			$myLog->save();
		}
		catch (\Exception $e)
		{
			return false;
		}
		
		return true;
    }
	
	public static function danger($params)
	{
		$params['type'] = 'danger';
		
		self::insertLog($params);
	}
	
	public static function warning($params)
	{
		$params['type'] = 'warning';
		
		self::insertLog($params);
	}
	
	public static function info($params)
	{
		$params['type'] = 'info';
		
		self::insertLog($params);
	}
	
	public static function success($params)
	{
		$params['type'] = 'success';
		
		self::insertLog($params);
	}
}
