<?php

class Alias extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'aliases';
	
	/**
     * One to many relationship.
     *
     * @return Model
     */
    public function site()
    {
		return $this->belongsTo('Site');
    }
	
}
