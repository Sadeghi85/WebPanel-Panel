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
	
	public function insertLog($description, $userID = null, $domainID = null, $event = null, $cssType = null)
    {
		$this->description = $description;
		$this->user_id = $userID;
		$this->domain_id = $domainID;
		$this->event = $event;
		$this->type = $cssType;
		
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
