<?php

use Phinx\Seed\AbstractSeed;

class CategorySeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * http://docs.phinx.org/en/latest/seeding.html
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        $data = [];

        for ($i = 0; $i < 16; $i++) {
            $data[] = [
                'user_id'   => 1,
                'name'      => $faker->userName . '' . $faker->randomDigitNotNull()
            ];
        }

        $this->insert('categories', $data);
    }
}
