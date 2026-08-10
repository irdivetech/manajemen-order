<?php

namespace App\Services;

use App\Models\MasterMaterial;
use App\Models\MaterialUsageEstimate;
use Illuminate\Support\Collection;

class HppService
{
    /**
     * Calculate estimated total HPP based on material usage and current price.
     *
     * @param int|null $materialId
     * @param array $sizeDetails Array of size details containing 'size_id' and 'quantity'
     * @return float Estimated total cost
     */
    public function calculateEstimatedHpp(?int $materialId, ?int $clothingCategoryId, array $sizeDetails): float
    {
        if (!$materialId || !$clothingCategoryId) {
            return 0;
        }

        $material = MasterMaterial::find($materialId);
        if (!$material || $material->price_per_unit <= 0) {
            return 0;
        }

        $totalEstimatedHpp = 0;
        $price = $material->price_per_unit;

        foreach ($sizeDetails as $detail) {
            $sizeId = $detail['size_id'] ?? null;
            $quantity = $detail['quantity'] ?? 0;

            if ($sizeId && $quantity > 0) {
                // Find estimate
                $estimate = MaterialUsageEstimate::where('material_id', $materialId)
                    ->where('clothing_category_id', $clothingCategoryId)
                    ->where('size_id', $sizeId)
                    ->first();

                if ($estimate) {
                    $totalEstimatedHpp += ($estimate->estimated_usage * $price * $quantity);
                }
            }
        }

        return $totalEstimatedHpp;
    }
}
