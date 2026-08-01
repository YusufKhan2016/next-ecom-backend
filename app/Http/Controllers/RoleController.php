<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\json;

class RoleController extends Controller
{

    public function index()
    {
        try {
            $roleData = Role::latest()->with('permissions:id,name')->get();

            return response()->json([
                'success' => true,
                'message' => 'Roles retrieved successfully.',
                'data' => $roleData
            ], 200);
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
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('roles')->ignore($request->role_id),
            ],
            'permission_ids' => 'nullable|array',
            'permission_ids.*' => 'exists:permissions,id'
        ]);

        try {
            $data = [
                'name' => $request->name,
                'guard_name' => 'web'
            ];

            if(!$request->filled('role_id')) {
                $data['code'] = $this->generateCode(Role::class, "ROL");

                $role = Role::create($data);
            } else {
                $role = Role::findOrFail($request->role_id);

                if($role->id == 1) {
                    throw new \Exception("Super Admin cannot be modified.");
                }

                $role->update($data);
            }

            $permissions = Permission::whereIn('id', $request->permission_ids)->get();
            $role->syncPermissions($permissions ?? []);

            return response()->json([
                'success' => true,
                'message' => 'Role Saved Successfully.',
                'data'    => $role
            ]);

        } catch (\Throwable $th) {

            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => null
            ]);
        }
    }


    public function show(Role $role)
    {
        try {

            $role->load('permissions:id,name');

            $permissions = $role->permissions->pluck('name')->values();

            $menus = Menu::whereNull('parent_id')
                ->with('children')
                ->orderBy('sort_order')
                ->get();

            $filteredMenus = $this->filterMenus($menus, $permissions);
            
            return response()->json([
                'success' => true,
                'message' => 'Fetched the role data successfully.',
                'data' => [
                    'id' => $role->id,
                    'name' => $role->name,
                    'code' => $role->code,
                    'permissions' => $role->permissions->pluck('name')->toArray(),
                    'menus' => $filteredMenus
                ]
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => null
            ]);
        }
    }


    public function destroy(string $id)
    {
        try {
            $role = Role::findOrFail($id);

            if($role->id == 1) {
                throw new \Exception("Super Admin cannot be deleted.");
            }

            if ($role->users()->exists()) {
                throw new Exception(
                    'This role is assigned to users. Remove it from all users first.'
                );
            }

            $role->delete();

            return response()->json([
                'success' => true,
                'message' => 'Succesfully deleted'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }
}
