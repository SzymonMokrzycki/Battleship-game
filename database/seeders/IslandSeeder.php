<?php

namespace Database\Seeders;

use App\Models\Island;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class IslandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $caribbeanFile = File::get(storage_path('app/islands/caribbeanIslands.json'));
        $bahamasFile = File::get(storage_path('app/islands/bahamasIslands.json'));
        $falklandsFile = File::get(storage_path('app/islands/faklandIslands.json'));
        $indonesiaFile = File::get(storage_path('app/islands/indonesiaIslands.json'));
        $philipinesFile = File::get(storage_path('app/islands/philipinesIslands.json'));
        $vanatuFile = File::get(storage_path('app/islands/vanatuIslands.json'));
        
        $caribbean = json_decode($caribbeanFile);
        $bahamas = json_decode($bahamasFile);
        $falklands = json_decode($falklandsFile);
        $indonesia = json_decode($indonesiaFile);
        $philipines = json_decode($philipinesFile);
        $vanatu = json_decode($vanatuFile);

        $islandsData = [];

        foreach($caribbean as $island){
            $data = ["name" => $island->name, "world_id" => 1];
            array_push($islandsData, $data);
        }

        foreach($bahamas as $island){
            $data = ["name" => $island->name, "world_id" => 3];
            array_push($islandsData, $data);
        }

        foreach($falklands as $island){
            $data = ["name" => $island->name, "world_id" => 4];
            array_push($islandsData, $data);
        }

        foreach($indonesia as $island){
            $data = ["name" => $island->name, "world_id" => 6];
            array_push($islandsData, $data);
        }

        foreach($philipines as $island){
            $data = ["name" => $island->name, "world_id" => 2];
            array_push($islandsData, $data);
        }

        foreach($vanatu as $island){
            $data = ["name" => $island->name, "world_id" => 5];
            array_push($islandsData, $data);
        }

        foreach($islandsData as $island) {
            Island::create($island);
        }
    }
}
