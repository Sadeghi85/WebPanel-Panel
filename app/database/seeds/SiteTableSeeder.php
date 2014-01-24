<?php

class SiteTableSeeder extends Seeder {

    public function run()
    {
        DB::table('sites')->delete();
		
		$site = new Site;
		
		$site->tag = 'web001';
		$site->server_name = 'example.com:80';
		
		$site->save();
    }
}