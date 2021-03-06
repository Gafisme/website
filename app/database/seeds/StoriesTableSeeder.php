<?php
use Faker\Factory as Faker;

class StoriesTableSeeder extends Seeder {

	public function run()
	{
		$this->command->info('Deleting existing Stories table ...');
		DB::statement('SET FOREIGN_KEY_CHECKS = 0'); 
		Story::truncate();
		DB::statement('SET FOREIGN_KEY_CHECKS = 1');

		$faker = Faker::create();

		$this->command->info('Seeding Stories table using Faker...');
		foreach(range(1, 10) as $index)
		{
			Story::create([
				'represent_txt' => $faker->paragraph(5),
				'frequence_txt' => $faker->paragraph(5),
				'user_id' => $faker->randomNumber(1, 100),
			]);
		}
	}

}