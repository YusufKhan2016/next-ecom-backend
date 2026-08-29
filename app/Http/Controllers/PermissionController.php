<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{

    public function index()
    {
        try {

            $permissions = Permission::select('id', 'name', 'guard_name')
                ->get()
                ->groupBy(function ($permission) {
                    return explode('.', $permission->name)[0];
                })->toArray();

//            $permissions = $permissions->pluck('name')->values();

//            $menus = Menu::whereNull('parent_id')
//                ->with('children')
//                ->orderBy('sort_order')
//                ->get();

//            $filteredMenus = $this->filterMenus($menus, $permissions);

            return response()->json([
                'success' => true,
                'message' => 'Permissions fetched successfully.',
//                'data' => [
//                    'permissions' => $permissions,
//                    'menus' => $filteredMenus
//                ]
                'data' => $permissions
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => []
            ]);
        }
    }

    public function store(Request $request)
    {
        //
    }

    public function show(string $id)
    {
        //
    }

    public function destroy(string $id)
    {
        //
    }
}
