<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\GrowthRequest;
use App\Models\Growth;
use Illuminate\Http\Request;

class GrowthController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $growths = Growth::with('child')->get();
            if ($growths->isEmpty()) {
                return response()->json([
                    'message' => 'No growth records found.',
                ], 404);
            }
            return response()->json([
                'data' => $growths,
                'message' => 'Growth records retrieved successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while retrieving growth records.',
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
    public function store(GrowthRequest $request)
    {
        try {
            $growth = Growth::create($request->validated());
            if (!$growth) {
                return response()->json([
                    'message' => 'Failed to create growth record.',
                ], 500);
            }
            return response()->json([
                'data' => $growth,
                'message' => 'Growth record created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while creating growth record.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
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
    public function update(Request $request, string $id)
    {
        try {
            $growth = Growth::findOrFail($id);
            $growth->update($request->validated());
            return response()->json([
                'data' => $growth,
                'message' => 'Growth record updated successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while updating growth record.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $growth = Growth::findOrFail($id);
            $growth->delete();
            return response()->json([
                'message' => 'Growth record deleted successfully.',
            ], 200);
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'An error occurred while deleting growth record.',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
