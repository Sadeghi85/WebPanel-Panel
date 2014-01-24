<?php

class AliasTableSeeder extends Seeder {

    public function run()
    {
        DB::table('aliases')->delete();
		
		$alias = new Alias;
		
		$alias->site_id = 1;
		$alias->alias = 'example.com';
		$alias->port = '80';
		
		$alias->save();
		
		//////////////////
		
		$alias = new Alias;
		
		$alias->site_id = 1;
		$alias->alias = 'www.example.com';
		$alias->port = '80';
		
		$alias->save();
    }
}