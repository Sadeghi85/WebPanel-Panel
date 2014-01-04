<?php

class DatabaseSeeder extends Seeder {

	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Eloquent::unguard();

		$this->call('GroupTableSeeder');
        $this->command->info('Groups table seeded!');

		$this->call('UserTableSeeder');
        $this->command->info('Users table seeded!');
		
		$this->call('DomainTableSeeder');
        $this->command->info('Domains table seeded!');
	}
}