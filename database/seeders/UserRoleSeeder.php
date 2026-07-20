<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class UserRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superAdmin = User::where('email', 'hasanrafsun123@gmail.com')->first();

        if($superAdmin) 
        {
            $superAdmin->assignRole('Super Admin');
        }
    }
}
