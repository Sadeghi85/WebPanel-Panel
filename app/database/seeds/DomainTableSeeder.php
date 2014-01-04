<?php

class DomainTableSeeder extends Seeder {

    public function run()
    {
        DB::table('domains')->delete();
		
		$domain = new Domain;
		
		$domain->name = 'example.com';
		$domain->alias = json_encode(array('www.example.com'), JSON_FORCE_OBJECT);
		
		$domain->save();
    }
}