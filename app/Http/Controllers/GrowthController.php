<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\GrowthRequest;
use App\Models\Children;
use App\Models\Growth;
use App\Services\WHOZScoreCalculator;
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
            $child = Children::findOrFail($request->child_id);
            if (!$child) {
                return response()->json([
                    'success' => false,
                    'message' => 'Child not found.',
                ], 404);
            }
            $growth = Growth::create($request->validated());
            if (!$growth) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to create growth record.',
                ], 500);
            }
            return response()->json([
                'success' => true,
                'data' => $growth,
                'message' => 'Growth record created successfully.',
            ], 201);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
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

    public function getGrowthReport(string $childId)
{
    try {
        $child = Children::with('growths')->find($childId);

        if (!$child || $child->growths->isEmpty()) {
            return response()->json([
                'message' => 'No growth records found for this child.',
            ], 404);
        }

        $csvPath = storage_path('app/data/lhfa-boys-zscore-expanded-tables.csv');
        $whoData = WHOZScoreCalculator::loadDataFromCSV($csvPath);

        $growthData = $child->growths->map(function ($growth) use ($child, $whoData) {
            // hitung usia dalam hari dari tanggal lahir sampai tanggal measurement
            $ageInDays = \Carbon\Carbon::parse($child->birth_date)
                ->diffInDays(\Carbon\Carbon::parse($growth->measurement_date));

            $resultZScore = WHOZScoreCalculator::hitungZScoreUntukUsia(
                $growth->height,
                $ageInDays,
                $whoData
            );
            return [
                'weight' => $growth->weight,
                'height' => $growth->height,
                'age_in_months' => floor($ageInDays / 30),
                'z_score' => $resultZScore['z_score'],
                'status' => $resultZScore['status'],
                'measurement_date' => $growth->measurement_date
            ];
        });



        return response()->json([
            'success' => true,
            'data' => $growthData,
            'message' => 'Growth report retrieved successfully.',
        ], 200);

    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
        ], 500);
    }
}

}
