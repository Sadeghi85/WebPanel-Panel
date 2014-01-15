<?php

use Cartalyst\Sentry\Groups\GroupExistsException;
use Cartalyst\Sentry\Groups\GroupNotFoundException;
use Cartalyst\Sentry\Groups\NameRequiredException;

class GroupsController extends RootController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		// Grab all the groups
		$groups = Sentry::getGroupProvider()->createModel()->paginate();

		// Show the page
		return View::make('app.groups.index', compact('groups'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		// Get all the available permissions
		$permissions = Config::get('permissions');
		$this->encodeAllPermissions($permissions, true);

		// Selected permissions
		$selectedPermissions = Input::old('permissions', array());

		// Show the page
		return View::make('app.groups.create', compact('permissions', 'selectedPermissions'));
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		// Declare the rules for the form validation
		$rules = array(
			'name' => 'required|between:3,127|alpha_dash',
		);

		// Create a new validator instance from our validation rules
		$validator = Validator::make(Input::all(), $rules);

		// If validation fails, we'll exit the operation now.
		if ($validator->fails())
		{
			// Ooops.. something went wrong
			return Redirect::back()->withInput()->withErrors($validator);
		}
		
		try
		{
			// We need to reverse the UI specific logic for our
			// permissions here before we create the user.
			$permissions = Input::get('permissions', array());
			$this->decodePermissions($permissions);
			app('request')->request->set('permissions', $permissions);

			// Get the inputs, with some exceptions
			$inputs = Input::only('name', 'permissions');

			// Was the group created?
			if (Sentry::getGroupProvider()->create($inputs))
			{
				// Log
				$fullname = trim(Sentry::getUser()->fullName());
				$username = Sentry::getUser()->username . ($fullname ? '('.$fullname.')' : '');
				$myLog = new MyLog;
				$myLog->insertLog(sprintf('User [%s] has created the Group [%s].', $username, $inputs['name']), Sentry::getUser()->id, null, 'Create Group', 'info');
				
				// Redirect to the group management page
				return Redirect::route('groups.index')->with('success', Lang::get('groups/messages.success.create'));
			}

			// Redirect to the new group page
			return Redirect::back()->with('error', Lang::get('groups/messages.error.create'));
		}
		catch (NameRequiredException $e)
		{
			$error = 'group_name_required';
		}
		catch (GroupExistsException $e)
		{
			$error = 'group_exists';
		}

		// Redirect to the group create page
		return Redirect::back()->withInput()->with('error', Lang::get('groups/messages.error.'.$error));
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		//
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		try
		{
			// Get the group information
			$group = Sentry::getGroupProvider()->findById($id);

			// Get all the available permissions
			$permissions = Config::get('permissions');
			$this->encodeAllPermissions($permissions, true);

			// Get this group permissions
			$selectedPermissions = $group->getPermissions();
			$this->encodePermissions($selectedPermissions);
			$selectedPermissions = array_merge($selectedPermissions, Input::old('permissions', array()));
		}
		catch (GroupNotFoundException $e)
		{
			// Prepare the error message
			$error = Lang::get('groups/messages.error.group_not_found', compact('id'));
			
			// Redirect to the groups management page
			return Redirect::route('groups.index')->with('error', $error);
		}

		// Show the page
		return View::make('app.groups.edit', compact('group', 'permissions', 'selectedPermissions'));
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function update($id)
	{
		// We need to reverse the UI specific logic for our
		// permissions here before we update the group.
		$permissions = Input::get('permissions', array());
		$this->decodePermissions($permissions);
		app('request')->request->set('permissions', $permissions);

		try
		{
			// Get the group information
			$group = Sentry::getGroupProvider()->findById($id);
		}
		catch (GroupNotFoundException $e)
		{
			// Redirect to the groups management page
			return Rediret::route('groups.index')->with('error', Lang::get('groups/messages.error.group_not_found', compact('id')));
		}

		$groupNameToLog = $group->name;
		
		// Declare the rules for the form validation
		$rules = array(
			'name' => 'required|between:3,127|alpha_dash',
		);

		// Create a new validator instance from our validation rules
		$validator = Validator::make(Input::all(), $rules);

		// If validation fails, we'll exit the operation now.
		if ($validator->fails())
		{
			// Ooops.. something went wrong
			return Redirect::back()->withInput()->withErrors($validator);
		}

		try
		{
			// Update the group data
			$group->name        = Input::get('name');
			$group->permissions = Input::get('permissions');

			// Was the group updated?
			if ($group->save())
			{
				// Log all this group's users out
				$users = Sentry::findAllUsersInGroup($group);
				
				foreach ($users as $user)
				{
					$user->persist_code = null;
					$user->save();
				}
				
				// Log
				$fullname = trim(Sentry::getUser()->fullName());
				$username = Sentry::getUser()->username . ($fullname ? '('.$fullname.')' : '');
				$myLog = new MyLog;
				$myLog->insertLog(sprintf('User [%s] has edited the Group [%s].%sCurrent Status:%s%s', $username, $groupNameToLog, "\r\n\r\n", "\r\n\r\n", print_r(Sentry::getGroupProvider()->findById($id)->toArray(), true)), Sentry::getUser()->id, null, 'Edit Group', 'info');
			
				// Redirect to the group page
				return Redirect::route('groups.index')->with('success', Lang::get('groups/messages.success.update'));
			}
			else
			{
				// Redirect to the group page
				return Redirect::back()->with('error', Lang::get('groups/messages.error.update'));
			}
		}
		catch (NameRequiredException $e)
		{
			$error = Lang::get('groups/messages.error.group_name_required');
		}

		// Redirect to the group page
		return Redirect::back()->withInput()->with('error', $error);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function destroy($id)
	{
		try
		{
			// Get group information
			$group = Sentry::getGroupProvider()->findById($id);

			// Delete the group
			$group->delete();
			
			// Log
			$fullname = trim(Sentry::getUser()->fullName());
			$username = Sentry::getUser()->username . ($fullname ? '('.$fullname.')' : '');
			$myLog = new MyLog;
			$myLog->insertLog(sprintf('User [%s] has deleted the Group [%s].', $username, $group->name), Sentry::getUser()->id, null, 'Delete Group', 'warning');

			// Redirect to the group management page
			return Redirect::back()->with('success', Lang::get('groups/messages.success.delete'));
		}
		catch (GroupNotFoundException $e)
		{
			// Prepare the error message
			$error = Lang::get('groups/messages.error.group_not_found', compact('id'));
			
			// Redirect to the group management page
			return Redirect::back()->with('error', $error);
		}
	}

}