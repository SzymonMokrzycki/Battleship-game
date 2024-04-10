<?php

namespace Database\Seeders;

use App\Models\Topic;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class TopicsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $topics = [
            [
                "name" => "Tactics advice",
                "user_id" => 1
            ],
            [
                "name" => "Questions",
                "user_id" => 1
            ],
            [
                "name" => "Technical aspects",
                "user_id" => 1
            ]
        ];

        foreach($topics as $topic) {
            Topic::create($topic);
        }
    }
}
