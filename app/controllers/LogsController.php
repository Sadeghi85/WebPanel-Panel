<?php

class LogsController extends RootController {

	/**
	 * Display a listing of the resource.
	 *
	 * @return Response
	 */
	public function index()
	{
		// Grab all the logs
		$logs = MyLog::newest()->with('domain', 'user')->paginate();

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