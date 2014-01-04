<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//Schema::table('domain_user', function(Blueprint $table)
		Schema::create('domain_user', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('domain_id');
			$table->integer('user_id');
			
			$table->timestamps();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		// Schema::table('domain_user', function(Blueprint $table)
		// {
			
		// });
		
		Schema::drop('domain_user');
	}

}