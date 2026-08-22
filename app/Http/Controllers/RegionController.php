<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class RegionController extends Controller
{
    /**
     * Get all provinces
     */
    public function getProvinces(): JsonResponse
    {
        if (!Storage::exists('regions/provinces.json')) {
            return response()->json([]);
        }
        
        $data = json_decode(Storage::get('regions/provinces.json'), true);
        return response()->json($data);
    }

    /**
     * Get cities by province id
     */
    public function getCities($provinceId): JsonResponse
    {
        $path = "regions/cities/{$provinceId}.json";
        if (!Storage::exists($path)) {
            return response()->json([]);
        }
        
        $data = json_decode(Storage::get($path), true);
        return response()->json($data);
    }

    /**
     * Get districts by city id
     */
    public function getDistricts($cityId): JsonResponse
    {
        $path = "regions/districts/{$cityId}.json";
        if (!Storage::exists($path)) {
            return response()->json([]);
        }
        
        $data = json_decode(Storage::get($path), true);
        return response()->json($data);
    }
}
