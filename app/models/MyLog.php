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
    public function domain()
    {
		return $this->belongsTo('Domain');
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
	
	public function insertLog($params)
    {
		$this->description = isset($params['description']) ? $params['description'] : '';
		$this->user_id = isset($params['user_id']) ? $params['user_id'] : null;
		$this->domain_id = isset($params['domain_id']) ? $params['domain_id'] : null;
		$this->event = isset($params['event']) ? $params['event'] : null;
		$this->type = isset($params['type']) ? $params['type'] : null;
		
		try
		{
			$this->save();
		}
		catch (\Exception $e)
		{
			return false;
		}
		
		return true;
    }
}
