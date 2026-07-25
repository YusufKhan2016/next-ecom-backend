<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{

    public function index()
    {
        try {
            $users = User::with('roles:id,name')->latest()->get();

            return response()->json([
                'success' => true,
                'message' => 'Users list retrieved successfully.',
                'data' => $users
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
        $request->validate([
            'name'  => 'required|string|max:255',
            'email' => [
                'required','email',
                Rule::unique('users')->ignore($request->user_id),
            ],
            'phone' => 'string|nullable',
            'password' => $request->user_id
                ? 'nullable|string|min:3'
                : 'required|string|min:3',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|boolean'
        ]);

        try {
            $data = [
                'name'  => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'status' => $request->status
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($request->password);
            }

            if(!$request->filled('user_id')) {
                $data['code'] = $this->generateCode(User::class, "USR");

                $userData = User::create($data);
            } else {
                $userData = User::findOrFail($request->user_id);
                $userData->update($data);
            }

            $role = Role::findOrFail($request->role_id);
            $userData->syncRoles([$role])->load('roles:id,name');

            return response()->json([
                'success' => true,
                'message' => 'User saved successfully.',
                'data' => $userData
            ]);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
                'data' => null
            ]);
        }
    }

    public function show(string $id)
    {
        try {
            
            $user = User::findOrFail($id);
            $user->load('roles');
            $role = $user->roles->first();
            $permissions = $user->getAllPermissions()
                ->map(function ($permission) {
                    return [
                        'id' => $permission->id,
                        'name' => $permission->name,
                    ];
                })
                ->values();

            return response()->json([
                'success' => true,
                'message' => 'User retrieved successfully.',
                'data' => [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'status' => $user->status,
                    'role_id' => optional($user->roles->first())->id,
                    'role' => [
                        'id' => $role->id,
                        'name' => $role->name,
                        'code' => $role->code,
                        'guard_name' => $role->guard_name,
                    ],
                    'permisssions' => $permissions
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
            $user = User::findOrFail($id);

            if($user->id == auth()->id()) {
                throw new \Exception("You cannot delete you own account.");
            } else if($user->hasRole('Super Admin')){
                throw new \Exception("You cannot delete the Author user.");
            }

            $user->delete();

            return response()->json([
                'success' => true,
                'message' => 'Succesfully user deleted'
            ], 200);

        } catch (\Throwable $th) {
            return response()->json([
                'success' => false,
                'message' => $th->getMessage(),
            ]);
        }
    }
}
