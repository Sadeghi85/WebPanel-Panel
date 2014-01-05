<?php

class Domain extends Eloquent {

	/**
	 * The database table used by the model.
	 *
	 * @var string
	 */
	protected $table = 'domains';
	
	/**
     * Many to many relationship.
     *
     * @return Model
     */
    public function users()
    {
        // Second argument is the name of pivot table.
        // Third & forth arguments are the names of foreign keys.
        return $this->belongsToMany('User', 'domain_user', 'domain_id', 'user_id')->withTimestamps();
        
    }
	
	public function isActivated()
	{
		if ($this->activated == 1)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
	
	public function formatAlias($input = null)
	{
		if ($input)
		{
			$aliases = explode("\r\n", $input);
			
			$aliases = array_map('strtolower', $aliases);

			return json_encode($aliases, JSON_FORCE_OBJECT);
		}
		else
		{
			$aliases = json_decode($this->alias);
			
			$formattedAlias = '';
			
			foreach ($aliases as $alias)
			{
				$formattedAlias .= $alias.', ';
			}
			
			$formattedAlias = trim(trim($formattedAlias), ',');
			
			return $formattedAlias;
		}
	}
	
}
