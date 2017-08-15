<?php

use Phinx\Seed\AbstractSeed;

class ProductSeeder extends AbstractSeed
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

        for ($i = 0; $i < 128; $i++) {
            $data[] = [
                'category_id'   => $faker->numberBetween(1, 16),
                'user_id'       => 1,
                'name'          => $faker->sentence($nbWords = 3, $variableNbWords = true),
                'description'   => $faker->realText($maxNbChars = 256),
                'price'         => $faker->randomFloat(2, 10, 999)
            ];
        }

        $this->insert('products', $data);
    }
}
