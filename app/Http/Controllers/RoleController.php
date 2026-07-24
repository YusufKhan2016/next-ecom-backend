<?php

namespace App\Http\Controllers;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\json;

class RoleController extends Controller
{

    public function index()
    {
        try {
            $roleData = Role::latest()->get();

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
            ], 500);
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
            ]
        ]);

        try {
            $data = [
                'name' => $request->name,
                'guard_name' => 'web'
            ];

            if(!$request->filled('role_id')) {
                $role = Role::create($data);
            } else {
                $role = Role::findOrFail($request->role_id);

                $role->update($data);
            }

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
            ], 500);
        }
    }


    public function show(Role $role)
    {
        try {

            return response()->json([
                'success' => true,
                'message' => 'Fetched the role data successfully.',
                'data' => $role
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

            if($role->name == 'Super Admin') {
                throw new \Exception("Super Admin cannot be deleted.");
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
            ], 500);
        }
    }
}
