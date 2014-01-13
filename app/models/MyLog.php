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
	
	
	
}
