<?php

namespace Database\Seeders;

use App\Models\World;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class WorldsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $worlds = [
            [
                'name' => 'Caribbean',
                'status' => 'open'
            ],
            [
                'name' => 'Philipines',
                'status' => 'open'
            ],
            [
                'name' => 'Bahamas',
                'status' => 'open'
            ],
            [
                'name' => 'Falklands',
                'status' => 'open'
            ],
            [
                'name' => 'Vanatu',
                'status' => 'open'
            ],
            [
                'name' => 'Indonesia',
                'status' => 'open'
            ]
        ];

        foreach($worlds as $world) {
            World::create($world);
        }
    }
}
