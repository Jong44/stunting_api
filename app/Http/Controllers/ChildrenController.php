<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChildrenRequest;
use App\Models\Children;
use Illuminate\Http\Request;

class ChildrenController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try{
            $children = Children::get();
        if ($children->isEmpty()) {
            return response()->json([
                'message' => 'No children found.',
            ], 404);
        }
        return response()->json([
            'data' => $children,
            'message' => 'Children list retrieved successfully.',
        ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving children.',
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
    public function store(ChildrenRequest $request)
    {
        try {
            $child = Children::create($request->validated());
            if (!$child) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create child.',
                ], 500);
            }
            return response()->json([
                'success' => true,
                'data' => $child,
                'message' => 'Child created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while creating child.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $child = Children::with('parent', 'growths')->find($id);
        if (!$child) {
            return response()->json([
                'success' => false,
                'message' => 'Child not found.',
            ], 404);
        }
        return response()->json([
            'success' => true,
            'data' => $child,
            'message' => 'Child details retrieved successfully.',
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
    public function update(ChildrenRequest $request, string $id)
    {
        $child = Children::find($id);
        if (!$child) {
            return response()->json([
                'success' => false,
                'message' => 'Child not found.',
            ], 404);
        }

        try {
            $child->update($request->validated());
            return response()->json([
                'success' => true,
                'data' => $child,
                'message' => 'Child updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $child = Children::find($id);
        if (!$child) {
            return response()->json([
                'success' => false,
                'message' => 'Child not found.',
            ], 404);
        }

        try {
            $child->delete();
            return response()->json([
                'success' => true,
                'message' => 'Child deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getChildrenByParentId(string $parentId)
    {
        try {
            $children = Children::where('parent_id', $parentId)->get();
            if ($children->isEmpty()) {
                return response()->json([
                    'message' => 'No children found for this parent.',
                ], 404);
            }
            return response()->json([
                'success' => true,
                'data' => $children,
                'message' => 'Children list retrieved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving children.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
