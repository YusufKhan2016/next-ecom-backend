<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;

abstract class Controller
{
    public function filterMenus($menus, $permissions)
    {
        // dd($permissions);
        $permissions = collect($permissions)->toArray();

        return $menus
            ->map(function ($menu) use ($permissions) {

                if ($menu->children->count()) {
                    $menu->setRelation(
                        'children',
                        $this->filterMenus($menu->children, $permissions)
                    );
                }

                $hasPermission = !$menu->permission || in_array($menu->permission, $permissions);

                if ($hasPermission || $menu->children->count()) {

                    if ($menu->children->count() == 0 && $menu->route == 0) {
                        return null;
                    }

                    return $menu;
                }

                return null;
            })
            ->filter()
            ->values();
    }

    public function generateSlug($model, $title, $id = null)
    {
        $slug = Str::slug($title);

        $originalSlug = $slug;
        $count = 1;

        while (
            $model::withTrashed()
            ->where('slug', $slug)
            ->when($id, function ($query) use ($id) {
                return $query->where('id', '!=', $id);
            })
            ->exists()
        ) {
            $slug = $originalSlug . '-' . $count;
            $count++;
        }

        return $slug;
    }

    public function generateCode($model, $prefix = 'ROL')
    {
        $last = $model::latest('id')->first();
        $nextNumber = $last ? $last->id + 1 : 1;

        return $prefix . str_pad($nextNumber, 6, '0', STR_PAD_LEFT);
    }
}
