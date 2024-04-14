<?php

namespace Database\Seeders;


use App\Models\Admin;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class AdminTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        

        Schema::disableForeignKeyConstraints();
        DB::table('admins')->truncate();
        Schema::enableForeignKeyConstraints();

        Admin::create([
            'name' => 'admin',
            'username' => 'admin',
            'email' => 'admin@yopmail.com',
            'email_verified' => '1',
            'role' => 'editor',
            'password' => Hash::make('welcome@123'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        
    }
}
