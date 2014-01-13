<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLogsTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('logs', function(Blueprint $table)
		{
			$table->increments('id');
			
			// ->nullable() on these foreign keys is ok because the relation is one to many
			$table->integer('user_id')->unsigned()->nullable();
			$table->integer('domain_id')->unsigned()->nullable();
			
			$table->string('event')->nullable();
			$table->text('description');
			$table->string('type')->nullable();
			
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
		Schema::drop('logs');
	}

}