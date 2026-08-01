<?php

namespace Database\Seeders;

use App\Models\Menu;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $menus = json_decode(
            file_get_contents(
                storage_path('data/administration/menus.json')
            ),
            true
        );

        $this->createMenus($menus);
    }

    private function createMenus(array $menus, $parentId = null)
    {
        foreach ($menus as $index => $menu) {

            $createdMenu = Menu::create([
                'title'      => $menu['title'],
                'slug'       => $menu['slug'],
                'icon'       => $menu['icon'] ?? null,
                'route'      => $menu['route'] ?? null,
                'permission' => $menu['permission'] ?? null,
                'parent_id'  => $parentId,
                'sort_order' => $index,
                'status'     => true,
            ]);

            if (!empty($menu['children'])) {
                $this->createMenus($menu['children'], $createdMenu->id);
            }
        }
    }
}
