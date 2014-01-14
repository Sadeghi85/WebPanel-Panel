<?php

return array(

	'Domain' => array(
		array(
			'permission' => 'domain.create',
			'label'      => 'Create',
			'allow'      => 0,
		),
		
		array(
			'permission' => 'domain.edit',
			'label'      => 'Edit',
			'allow'      => 0,
		),
		
		array(
			'permission' => 'domain.delete',
			'label'      => 'Delete',
			'allow'      => 0,
		),
	),
	
	'Log' => array(
		array(
			'permission' => 'log.self',
			'label'      => 'Only Self',
			'allow'      => 1,
		),
		
		array(
			'permission' => 'log.nonroot',
			'label'      => 'All Non-Root',
			'allow'      => 0,
		),
		
		array(
			'permission' => 'log.all',
			'label'      => 'All including Root',
			'allow'      => 0,
		),
	),


);
