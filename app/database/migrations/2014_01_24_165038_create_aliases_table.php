<?php

use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAliasesTable extends Migration {

	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('aliases', function(Blueprint $table)
		{
			$table->increments('id');
			
			// We'll need to ensure that MySQL uses the InnoDB engine to
			// support the indexes, other engines aren't affected.
			$table->engine = 'InnoDB';
			
			$table->integer('site_id')->unsigned();
			
			$table->string('alias');
			$table->integer('port')->unsigned();
			
			$table->unique(array('alias', 'port'));
			
			$table->index('site_id');
			
			$table->foreign('site_id')->references('id')->on('sites')->onDelete('cascade');
			
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
		Schema::drop('aliases');
	}

}