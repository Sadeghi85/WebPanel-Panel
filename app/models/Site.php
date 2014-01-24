<?php

class Site extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'sites';
	
	/**
     * Many to many relationship.
     *
     * @return Model
     */
    public function users()
    {
        // Second argument is the name of pivot table.
        // Third & forth arguments are the names of foreign keys.
        return $this->belongsToMany('User', 'site_user', 'site_id', 'user_id')->withTimestamps();
        
    }
	
	/**
	 * One to many relationship.
	 *
	 * @return Model
	 */
	public function logs()
    {
        return $this->hasMany('MyLog', 'site_id');
    }
	
	/**
	 * One to many relationship.
	 *
	 * @return Model
	 */
	public function aliases()
    {
        return $this->hasMany('Alias', 'site_id');
    }
	
	public function isActivated()
	{
		return ($this->activated == 1) ? true : false;
	}
	
	public static function formatAlias($aliases)
	{
		return trim(trim(implode(', ', $aliases)), ',');
	
	}
	
	// public static function formatAlias($input)
	// {
		// if ($aliases = @json_decode(trim($input)))
		// {
			// $formattedAlias = '';
			
			// foreach ($aliases as $alias)
			// {
				// $formattedAlias .= $alias.', ';
			// }
			
			// $formattedAlias = trim(trim($formattedAlias), ',');
			
			// return $formattedAlias;
		// }
		// else
		// {
			// $aliases = explode("\r\n", trim($input));
			
			// $aliases = array_map('strtolower', $aliases);

			// return json_encode($aliases);
		// }
	// }
	
}
