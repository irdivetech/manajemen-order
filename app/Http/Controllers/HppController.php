<?php

namespace App\Http\Controllers;

use App\Services\HppService;
use Illuminate\Http\Request;

class HppController extends Controller
{
    public function __construct(private HppService $hppService)
    {
    }

    /**
     * Calculate estimated HPP via API
     */
    public function calculateApi(Request $request)
    {
        $request->validate([
            'material_id' => 'required|exists:master_materials,id',
            'clothing_category_id' => 'required|exists:master_clothing_categories,id',
            'size_details' => 'required|array',
            'size_details.*.size_id' => 'required|exists:master_sizes,id',
            'size_details.*.quantity' => 'required|integer|min:1',
        ]);

        $estimatedHpp = $this->hppService->calculateEstimatedHpp(
            $request->input('material_id'),
            $request->input('clothing_category_id'),
            $request->input('size_details')
        );

        return response()->json([
            'estimated_hpp' => $estimatedHpp
        ]);
    }
}
