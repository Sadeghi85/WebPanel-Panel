<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSiteUserTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//Schema::table('site_user', function(Blueprint $table)
		Schema::create('site_user', function(Blueprint $table)
		{
			//$table->increments('id');
			
			$table->integer('site_id')->unsigned();
			$table->integer('user_id')->unsigned();
			
			// We'll need to ensure that MySQL uses the InnoDB engine to
			// support the indexes, other engines aren't affected.
			$table->engine = 'InnoDB';
			$table->primary(array('site_id', 'user_id'));
			
			$table->index('site_id');
			$table->index('user_id');
			
			$table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
			$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
			
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
		// Schema::table('site_user', function(Blueprint $table)
		// {
			
		// });
		
		Schema::drop('site_user');
	}

}