<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthenticatedSessionController extends Controller
{
    /**
     * Handle an incoming authentication request.
     */
    public function store(Request $request)
    {
        $request->validate([
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ]);
    
        if (!auth()->attempt($request->only('email', 'password'))) {
            return response()->json([
                'message' => 'Invalid login credentials'
            ], 422);
        }
    
        $user = User::where('email', $request->email)->first();

        // Load workspace with relationships
        $user->load([
            'workspace:id,name,created_at',
            'workspace.users:id,workspace_id,name,email,role',
            'assignedItems:id,workspace_id,assigned_user_id,title,status,created_at'
        ]);
        
        $accessToken = $user->createToken('access_token')->plainTextToken;
    
    
        return response()->json([
            'access_token' => $accessToken,
            'message' => 'Login successful',
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'workspace_id' => $user->workspace_id,
                'created_at' => $user->created_at,
                'workspace' => $user->workspace ? [
                    'id' => $user->workspace->id,
                    'name' => $user->workspace->name,
                    'created_at' => $user->workspace->created_at,
                    'users_count' => $user->workspace->users->count(),
                    'users' => $user->workspace->users->map(fn($u) => [
                        'id' => $u->id,
                        'name' => $u->name,
                        'email' => $u->email,
                        'role' => $u->role,
                    ])
                ] : null,
                'assigned_items_count' => $user->assignedItems->count(),
                'assigned_items' => $user->assignedItems->map(fn($item) => [
                    'id' => $item->id,
                    'title' => $item->title,
                    'status' => $item->status,
                    'created_at' => $item->created_at,
                ])
            ]
        ]);
    }

    public function me()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json([
                'message' => 'Unauthenticated'
            ], 401);
        }

        $user->load([
            'workspace:id,name,created_at',
            'workspace.users:id,workspace_id,name,email,role',
            'assignedItems:id,workspace_id,assigned_user_id,title,status,created_at'
        ]);

        return response()->json([
            'user' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->role,
                'workspace_id' => $user->workspace_id,
                'created_at' => $user->created_at,
                'workspace' => $user->workspace ? [
                    'id' => $user->workspace->id,
                    'name' => $user->workspace->name,
                    'created_at' => $user->workspace->created_at,
                    'users_count' => $user->workspace->users->count(),
                    'users' => $user->workspace->users->map(fn($u) => [
                        'id' => $u->id,
                        'name' => $u->name,
                        'email' => $u->email,
                        'role' => $u->role,
                    ])
                ] : null,
                'assigned_items_count' => $user->assignedItems->count(),
                'assigned_items' => $user->assignedItems->map(fn($item) => [
                    'id' => $item->id,
                    'title' => $item->title,
                    'status' => $item->status,
                    'created_at' => $item->created_at,
                ])
            ]
        ]);
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request)
    {
        if(!$request->user()){
            return response()->json([
                'message' => 'Unauthorized'
            ], 401);
        }

        $request->user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully']);
    }
}