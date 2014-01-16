<?php

class DomainsController extends AuthorizedController {

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
			$inputs = explode("\r\n", $value);
			
			foreach ($inputs as $input)
			{
				if ( ! preg_match('#^([a-z0-9][-a-z0-9]*\.)+[a-z]{2,5}$#i', $input))
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
		//'name'       => 'required|custom.domain|unique:domains,name',
		'name'       => 'required|custom.domain',
		'alias'       => 'domain',
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
			$domains = Domain::paginate();
		}
		else
		{
			$domains = Sentry::getUser()->domains()->paginate();
		}

		// Show the page
		return View::make('app.domains.index', compact('domains'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		if ( ! (Group::isRoot() or Sentry::getUser()->hasAccess('domain.create')))
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
		return View::make('app.domains.create', compact('users', 'selectedUsers', 'indexPage'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		if (Group::isRoot())
		{
			$this->validationRules['users'] = 'custom.exists_array:users,id';
		}
	
		// Create a new validator instance from our validation rules
		$validator = Validator::make(Input::all(), $this->validationRules);

		// If validation fails, we'll exit the operation now.
		if ($validator->fails())
		{
			// Ooops.. something went wrong
			return Redirect::back()->withInput()->withErrors($validator);
		}
		
		// Create domain
		$status = \Libraries\Sadeghi85\Domains::create(strtolower(Input::get('name')), Input::get('ipPort'), (int) Input::get('activated', 0));
		
		if ($status['status'] !== 0)
		{
			$error = sprintf('Code %s: %s', $status['line'], $status['message']);
			
			$error .= ($status['output'] ? sprintf(' <br><br><pre><code>%s</code></pre>', $status['output']) : '');
			
			// Redirect to the user creation page
			return Redirect::back()->withInput()->with('error', $error);
		}
		
		// Register domain in database
		$domain = new Domain;
		
		$domain->name = strtolower(Input::get('name'));
		$alias = Input::get('alias') ? Input::get('alias') : sprintf('www.%s', $domain->name);
		$domain->alias = $domain->formatAlias($alias);
		$domain->activated = (int) Input::get('activated', 0);
		
		$domain->save();
		
		if (Group::isRoot())
		{
			$users = Input::get('users', array());
			
			foreach ($users as $userId)
			{
				$domain->users()->attach($userId);
			}
		}
		else
		{
			$domain->users()->attach(Sentry::getUser()->id);
		}
		
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
		if ( ! (Group::isRoot() or Sentry::getUser()->hasAccess('domain.edit')))
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
		if ( ! (Group::isRoot() or Sentry::getUser()->hasAccess('domain.delete')))
		{
			App::abort(403);
		}
		
	}

}