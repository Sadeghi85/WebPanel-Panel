<?php

class LogsController extends AuthorizedController {

	/**
	 * Initializer.
	 *
	 * @return void
	 */
	public function __construct()
	{
		// Call parent
		parent::__construct();
	}
	
	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{	
		// $item_ids = DB::table('category_item')->where('category_id', '=', $category_id)->get('item_id');
		// $item_ids = array_map(function($arr){ return $arr->item_id; }, $item_ids);
		// $items = Item::where_in('id', $item_ids)->paginate(10);

		
		if (Sentry::getUser()->inGroup(Sentry::findGroupByName('Root')))
		{
			// Grab all the logs
			$logs = MyLog::newest()->with('domain', 'user')->paginate();
		}
		else
		{
			if (Sentry::getUser()->hasAccess('log.all'))
			{
				// Grab all the logs including Root
				$logs = MyLog::newest()->with('domain', 'user')->paginate();
			}
			elseif (Sentry::getUser()->hasAccess('log.nonroot'))
			{
				// Grab all the logs for users that belong to groups other than Root
				$groupIDs = Sentry::getGroupProvider()->createModel()->where('name', '<>', 'Root')->lists('id');
				$userIDs = DB::table('users_groups')->whereIn('group_id', $groupIDs)->lists('user_id');
				$logs = MyLog::whereIn('user_id', $userIDs)->newest()->with('domain', 'user')->paginate();
			}
			elseif (Sentry::getUser()->hasAccess('log.self'))
			{
				// Grab all the logs for this user only
				$logs = MyLog::where('user_id', '=', Sentry::getUser()->id)->newest()->with('domain', 'user')->paginate();
			}
			else
			{
				App::abort(403);
			}
		}
		
		// Show the page
		return View::make('app.logs.index', compact('logs'));
	}

	/**
	 * Show the form for creating a new resource.
	 *
	 * @return Response
	 */
	public function create()
	{
		//
	}

	/**
	 * Store a newly created resource in storage.
	 *
	 * @return Response
	 */
	public function store()
	{
		//
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function show($id)
	{
		// Grab all the logs
		$log = MyLog::findOrFail($id)->with('domain', 'user')->first();

		// Show the page
		return View::make('app.logs.show', compact('log'));
	}

	/**
	 * Show the form for editing the specified resource.
	 *
	 * @param  int  $id
	 * @return Response
	 */
	public function edit($id)
	{
		//
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
		$log = MyLog::findOrFail($id);

		// Delete the log
		$log->delete();

		// Prepare the success message
		$success = Lang::get('logs/messages.success.delete');

		// Redirect to the logs page
		return Redirect::back()->with('success', $success);
	}

}