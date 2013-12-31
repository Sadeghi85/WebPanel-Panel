<?php

return array(

	'error' => array(
		'create' => 'User couldn\'t be created.',
		'update' => 'User couldn\'t be updated.',
		'delete' => 'User couldn\'t be deleted.',
		
		'user_exists'        => 'User already exists!',
		'user_not_found'     => 'User [:id] does not exist.',
		'user_name_required' => 'The name field is required',
	),
	
	'success' => array(
		'create' => 'User is successfully created.',
		'update' => 'User is successfully updated.',
		'delete' => 'User is successfully deleted.',
	),
	

	'index' => array(
		'title' => 'Users',
		
		'header' => 'User Management',
		
		'create' => 'Create',
		
		'id' => 'Id',
		'first_name' => 'First Name',
		'last_name' => 'Last Name',
		'username' => 'Username',
		'activated' => 'Activated',
		'created_at' => 'Created at',
		'actions' => 'Actions',
	
	),
	
	'create' => array(
		'title' => 'Create a User',
		'header' => 'Create a New User',
		
		'back' => 'Back',
		
		'username' => 'Username',
		'password' => 'Password',
		'password_confirm' => 'Confirm Password',
		'first_name' => 'First Name',
		'last_name' => 'Last Name',
		'activated' => 'User Activated',
		'group' => 'Group',
		'select_group' => 'Select a Group',
		
	),
	
	'edit' => array(
		'title' => 'Edit User',
		'header' => 'Edit User',
		
		'back' => 'Back',
		
		'name' => 'Name',
		
	),
);
