<?php

use Cartalyst\Sentry\Groups\Eloquent\Group as SentryGroupModel;

class Group extends SentryGroupModel {

	public static function isRoot()
	{
		if (Sentry::check() and Sentry::getUser()->inGroup(Sentry::findGroupByName('Root')))
		{
			return true;
		}
		
		return false;
	}
}
