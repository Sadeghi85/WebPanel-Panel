<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSitesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//Schema::table('sites', function(Blueprint $table)
		Schema::create('sites', function(Blueprint $table)
		{
			$table->increments('id');
			
			$table->string('tag')->unique();
			$table->string('server_name')->unique(); // domain:port
			$table->boolean('activated')->default(0);
			
			$table->integer('quota')->default(0);
			$table->string('ftp_password')->nullable();
			$table->string('mysql_password')->nullable();
			
			// We'll need to ensure that MySQL uses the InnoDB engine to
			// support the indexes, other engines aren't affected.
			$table->engine = 'InnoDB';
			
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
		// Schema::table('sites', function(Blueprint $table)
		// {
			
		// });
		
		Schema::drop('sites');
	}

}