<?php

class OverviewController extends \BaseController {

	public function getOverview()
	{
		return View::make('app.overview');
	}

	
}