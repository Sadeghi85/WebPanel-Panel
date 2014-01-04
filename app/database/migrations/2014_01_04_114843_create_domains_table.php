<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDomainsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		//Schema::table('domains', function(Blueprint $table)
		Schema::create('domains', function(Blueprint $table)
		{
			$table->increments('id');
			
			$table->string('name')->unique();
			$table->text('alias')->nullable(); # JSON array
			$table->boolean('activated')->default(0);
			
			
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
		// Schema::table('domains', function(Blueprint $table)
		// {
			
		// });
		
		Schema::drop('domains');
	}

}