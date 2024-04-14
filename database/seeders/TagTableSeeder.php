<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TagTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $records = DB::table('tags')->count();
        if(!$records) {

            $data = [

                ['name' => 'bottomless mimosas'],
                ['name' => 'buffet'],
                ['name' => 'live dj'],
                ['name' => 'live music'],
                ['name' => 'indoor'],
                ['name' => 'outdoor'],
                ['name' => 'rooftop'],
            ];

            DB::table('tags')->insert($data);
        }
    }
}
