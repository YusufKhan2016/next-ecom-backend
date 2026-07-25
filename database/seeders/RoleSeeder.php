<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jsonString = File::get(storage_path('data/administration/roles.json'));

        $data = json_decode($jsonString, true);

        foreach ($data as $item) {
            $roleData = [
                'name' => $item['name'],
                'code' => $item['code'],
                'guard_name' => $item['guard_name']
            ];

            $role = Role::firstOrCreate($roleData);

            if($item['all_permissions'] == true)
            {
                $role->syncPermissions(Permission::all());
            }
        }

    }
}
