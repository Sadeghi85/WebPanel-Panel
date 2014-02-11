<?php

class SitesController extends AuthorizedController {

	/**
	 * Initializer.
	 *
	 * @return void
	 */
	public function __construct()
	{
		// Call parent
		parent::__construct();
		
		Validator::extend('custom.domain', function($attribute, $value, $parameters)
		{
			$inputs = explode("\r\n", trim($value));
			
			foreach ($inputs as $input)
			{
				if (preg_match('#^\d+(?:\.\d+)*$#', $input))
				{
					return false;
				}
				
				if ( ! preg_match('#^(?=.{1,255}$)[0-9A-Za-z](?:(?:[0-9A-Za-z]|\b-){0,61}[0-9A-Za-z])?(?:\.[0-9A-Za-z](?:(?:[0-9A-Za-z]|\b-){0,61}[0-9A-Za-z])?)*$#', $input))
				{
					return false;
				}
			}
			
			return true;
		});
		
		Validator::extend('custom.exists_array', function($attribute, $value, $parameters)
		{
			if (count($parameters) != 2) { return false; }
			
			if ( ! is_array($value))
			{
				$inputs = array($value);
			}
			else
			{
				$inputs = $value;
			}
			
			foreach ($inputs as $input)
			{
				$validator = Validator::make(array($attribute => $input), array($attribute => sprintf('exists:%s,%s', $parameters[0], $parameters[1])));

				if ($validator->fails()) { return false; }
			}
			
			return true;
		});
	}
	
	/**
	 * Declare the rules for the form validation
	 *
	 * @var array
	 */
	protected $validationRules = array(
		// TODO: add port to db, unique index on (name,port)
		//'port'        => 'required|integer|between:80,49151|unique:domains,port,NULL,id,name,$name', // http://stackoverflow.com/a/113237
		//'name'       => 'required|custom.domain|unique:domains,name,NULL,id,port,$port',
		//'alias'       => 'custom.domain',
		//'users'       => 'custom.exists_array:users,id',
	);
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		// Grab all the domains for current user
		if (Group::isRoot())
		{
			$sites = Site::with('aliases')->paginate();
		}
		else
		{
			$sites = Sentry::getUser()->sites()->with('aliases')->paginate();
		}

		// Show the page
		return View::make('app.sites.index', compact('sites'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		if ( ! (Group::isRoot() or Sentry::getUser()->hasAccess('site.create')))
		{
			App::abort(403);
		}
		
		$users = $selectedUsers = '';
		
		if (Group::isRoot())
		{
			// Get all the available users
			$users = Sentry::getUserProvider()->findAll();

			// Selected users
			$selectedUsers = Input::old('users', array());
		}
		
		$indexPage = '';
		if (preg_match('#page=(\d+)#', URL::previous(), $matches))
		{
			$indexPage = $matches[1];
		}

		// Show the page
		return View::make('app.sites.create', compact('users', 'selectedUsers', 'indexPage'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		if ( ! (Group::isRoot() or Sentry::getUser()->hasAccess('site.create')))
		{
			App::abort(403);
		}
		
		app('request')->request->set('port', trim(Input::get('port')));
		app('request')->request->set('quota', trim(Input::get('quota')));
		app('request')->request->set('server_name', strtolower(trim(Input::get('server_name'))));
		app('request')->request->set('aliases', implode("\r\n", array_unique(array_map('strtolower', array_filter(array_map('trim', explode("\r\n", Input::get('server_name')."\r\n".Input::get('aliases'))))))));
		
		// Validation rules
		$this->validationRules['port'] = 'required|integer|between:80,49151';
		// available when user is Root, hence "sometimes"
		$this->validationRules['users'] = 'sometimes|custom.exists_array:users,id';
		$this->validationRules['server_name'] = 'required|custom.domain';
		$this->validationRules['aliases'] = 'custom.domain';
		
		// Create a new validator instance from our validation rules
		$validator = Validator::make(Input::all(), $this->validationRules);

		// If validation fails, we'll exit the operation now.
		if ($validator->fails())
		{
			// Ooops.. something went wrong
			return Redirect::back()->withInput()->withErrors($validator);
		}
		
		$sitePort = Input::get('port');
		$siteServerName = Input::get('server_name').':'.$sitePort;
		$siteAliases = explode("\r\n", Input::get('aliases'));
		$siteActivate = (int) Input::get('activate', 0);
		
		foreach ($siteAliases as $siteAlias)
		{
			// Create a new validator instance from our validation rules
			$validator = Validator::make(array('aliases' => $siteAlias), array('aliases' => 'unique:aliases,alias,NULL,id,port,'.$sitePort));

			// If validation fails, we'll exit the operation now.
			if ($validator->fails())
			{
				$messages = $validator->messages();
				
				$m = new Illuminate\Support\MessageBag;
				$m->add('aliases', $siteAlias.' : '.$messages->first('aliases'));
				if ($siteAlias == Input::get('server_name')) $m->add('server_name', $siteAlias.' : '.$messages->first('aliases'));
				// Ooops.. something went wrong
				return Redirect::back()->withInput()->withErrors($m);
			}
		}

		// Find next available site tag
		if ( ! \Libraries\Sadeghi85\Sites\Shell::FindNextTag($errorMessage, $siteTag))
		{
			return Redirect::back()->withInput()->with('error', $errorMessage);
		}
		
		// Create Site
		if ( ! \Libraries\Sadeghi85\Sites\Create::create($errorMessage, array(
			'siteServerName'   => $siteServerName,
			'sitePort'   => $sitePort,
			'siteAliases'    => $siteAliases,
			'siteTag' => $siteTag,
			'siteActivate' => $siteActivate,
		)))
		{
			return Redirect::back()->withInput()->with('error', $errorMessage);
		}
		dd($errorMessage);
		try
		{
			// Register domain in database
			$domain = new Domain;
			
			$domain->name = $domainName;
			//$domain->ip_port = $domainIpPort;
			$domain->alias = $domainAlias;
			$domain->activated = $domainActivate;
			
			$domain->save();
		}
		catch (\Exception $e)
		{
			// Log
			$domainNameToLog = $domain->name;
			$myLog = new MyLog;
			$myLog->insertLog(
				array(
						'description' => sprintf('Domain [%s] is created, but couldn\'t register it in the database.', $domainNameToLog),
						'user_id'     => Sentry::getUser()->id,
						'domain_id'   => null,
						'event'       => 'Create Domain',
						'type'        => 'danger',
				)
			);
				
			$error = sprintf('Domain [%s] is created, but couldn\'t register it in the database.', $domainNameToLog);
			return Redirect::back()->withInput()->with('error', $error);
		}
		
		if (Group::isRoot())
		{
			$users = Input::get('users', array());
			
			foreach ($users as $userId)
			{
				try
				{
					$domain->users()->attach($userId);
				}
				catch (\Exception $e)
				{
				
				}
			}
		}
		else
		{
			$domain->users()->attach(Sentry::getUser()->id);
		}
		
		// Log
		$domainNameToLog = $domain->name;
		$currentUserUsername = Sentry::getUser()->usernameWithFullName();
		$myLog = new MyLog;
		$myLog->insertLog(
			array(
					'description' => sprintf('User [%s] has created the Domain [%s].%sCurrent Status:%s%s', $currentUserUsername, $domainNameToLog, "\r\n\r\n", "\r\n\r\n", print_r($domain->toArray(), true)),
					'user_id'     => Sentry::getUser()->id,
					'domain_id'   => $domain->id,
					'event'       => 'Create Domain',
					'type'        => 'info',
			)
		);
				
		// Prepare the success message
		$success = Lang::get('domains/messages.success.create');

		// Redirect to the user page
		return Redirect::route('domains.index', array('page' => input::get('indexPage', 1)))->with('success', $success);
		
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		if ( ! (Group::isRoot() or Sentry::getUser()->hasAccess('site.edit')))
		{
			App::abort(403);
		}
		
		
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		//
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		if ( ! (Group::isRoot() or Sentry::getUser()->hasAccess('site.delete')))
		{
			App::abort(403);
		}
		
	}

}