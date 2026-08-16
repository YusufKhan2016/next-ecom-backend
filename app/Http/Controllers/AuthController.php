<?php

namespace App\Http\Controllers;

use App\Models\Menu;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if(!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid Credentials'
            ], 401);
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        $permissions = $user
            ->getAllPermissions()
            ->pluck('name')
            ->values();

        $role = $user
            ->getRoleNames()
            ->values()
            ->first();

        $menus = Menu::whereNull('parent_id')
            ->with('children')
            ->orderBy('sort_order')
            ->get();


        $filteredMenus = $this->filterMenus($menus, $permissions);

        return response()->json([
            'success' => true,
            'message' => 'Login Successful',
            'data' => [
                'token' => $token,
                'user' => [
                    'id'    => $user->id,
                    'code'  => $user->code,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'status' => $user->status,
                ],
                'role' => $role,
                'permissions' => $permissions,
                'menus' => $filteredMenus
            ]
        ]);
    }

    public function getUser(Request $request)
    {
        $user = auth()->user();
        $permissions = $user
            ->getAllPermissions()
            ->pluck('name')
            ->values();

        $role = $user
            ->getRoleNames()
            ->values()
            ->first();

        $menus = Menu::where('parent_id', null)
            ->with('children')
            ->orderBy('sort_order')
            ->get();

        $filteredMenus = $this->filterMenus($menus, $permissions);

        return response()->json([
            'success' => true,
            'message' => 'User retrieved successfully.',
            'data' => [
                'user' => [
                    'id'    => $user->id,
                    'code'  => $user->code,
                    'name'  => $user->name,
                    'email' => $user->email,
                    'phone' => $user->phone,
                    'status' => $user->status,
                ],
                'role' => $role,
                'permissions' => $permissions,
                'menus' => $filteredMenus
            ]

        ], 200);
    }

    public function logout(Request $request)
    {
        $user = auth()->user();

        $user->currentAccessToken()->delete();

        return response()->json([
            'success' => false,
            'message' => 'Successfully logged out.'
        ],200);
    }

    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:3|confirmed'
        ]);

        $user = auth()->user();

        if(!Hash::check($request->current_password, $user->password))
        {
            return response()->json([
                'success' => false,
                'message
                ' => 'Current password is incorrect.'
            ], 422);
        }

        $user->update([
            'password' => Hash::make($request->password)
        ]);

        $user->tokens()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Password changed successfully. Please login again.'
        ], 200);
    }

}
