<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonString = File::get(storage_path('data/administration/users.json'));

        $data = json_decode($jsonString, true);
        
        foreach ($data as $item) {
            $users = [];
            $item['password'] = Hash::make($item['password']);
            User::updateOrCreate($users, $item);
        }
    }
}
