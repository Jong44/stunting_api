<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $users = User::with('children.growths')->get();
            if ($users->isEmpty()) {
                return response()->json([
                    'message' => 'No users found.',
                ], 404);
            }
            return response()->json([
                'data' => $users,
                'message' => 'Users list retrieved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving users.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(UserRequest $request)
    {
        try {
            $user = User::create($request->validated());
            if (!$user) {
                return response()->json([
                    'message' => 'Failed to create user.',
                ], 500);
            }
            return response()->json([
                'data' => $user,
                'message' => 'User created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating the user.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $user,
            'message' => 'User details retrieved successfully.',
        ], 200);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UserRequest $request, string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'User not found.',
            ], 404);
        }

        try {
            $user->update($request->validated());
            return response()->json([
                'success' => true,
                'data' => $user,
                'message' => 'User updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while updating the user.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $user = User::find($id);
        if (!$user) {
            return response()->json([
                'message' => 'User not found.',
            ], 404);
        }

        try {
            $user->delete();
            return response()->json([
                'message' => 'User deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting the user.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
