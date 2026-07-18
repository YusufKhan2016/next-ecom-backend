<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonString = File::get(storage_path('data/administration/permissions.json'));

        $data = json_decode($jsonString, true);
        foreach ($data as $item) {
            foreach ($item['permissions'] as $permission) { 

                $permissionData = [
                    'name' => "{$item['name']}.{$permission}",
                    'guard_name' => $item['guard_name']
                ];

                Permission::firstOrCreate($permissionData);
            }
        }
    }
}   
