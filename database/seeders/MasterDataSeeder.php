<?php

namespace Database\Seeders;

use App\Models\MasterClothingCategory;
use App\Models\MasterGender;
use App\Models\MasterMaterial;
use App\Models\MasterSize;
use App\Models\MasterSizeCategory;
use App\Models\MasterTrackingStatus;
use App\Models\MaterialUsageEstimate;
use App\Models\TrackingFlowRule;
use Illuminate\Database\Seeder;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Genders
        $genders = [
            ['code' => 'male', 'label' => 'Laki-laki'],
            ['code' => 'female', 'label' => 'Perempuan'],
            ['code' => 'child', 'label' => 'Anak-anak'],
        ];
        foreach ($genders as $gender) {
            MasterGender::updateOrCreate(['code' => $gender['code']], $gender);
        }

        // 2. Size Categories
        $sizeCategories = [
            ['name' => 'TK', 'sort_order' => 1],
            ['name' => 'SD', 'sort_order' => 2],
            ['name' => 'SMP', 'sort_order' => 3],
            ['name' => 'SMA / Dewasa', 'sort_order' => 4],
        ];
        foreach ($sizeCategories as $cat) {
            MasterSizeCategory::updateOrCreate(['name' => $cat['name']], $cat);
        }

        // 3. Sizes
        $sizes = [
            ['size_type' => 'standard', 'code' => 'XS', 'label' => 'XS', 'sort_order' => 1],
            ['size_type' => 'standard', 'code' => 'S', 'label' => 'S', 'sort_order' => 2],
            ['size_type' => 'standard', 'code' => 'M', 'label' => 'M', 'sort_order' => 3],
            ['size_type' => 'standard', 'code' => 'L', 'label' => 'L', 'sort_order' => 4],
            ['size_type' => 'standard', 'code' => 'XL', 'label' => 'XL', 'sort_order' => 5],
            ['size_type' => 'big', 'code' => 'XXL', 'label' => 'XXL', 'sort_order' => 6],
            ['size_type' => 'big', 'code' => 'XXXL', 'label' => 'XXXL', 'sort_order' => 7],
        ];
        foreach ($sizes as $size) {
            MasterSize::updateOrCreate(['code' => $size['code']], $size);
        }

        // 4. Materials
        $materials = [
            ['name' => 'Cotton Combed 30s', 'unit' => 'kg', 'price_per_unit' => 120000],
            ['name' => 'Drill', 'unit' => 'meter', 'price_per_unit' => 45000],
            ['name' => 'Fleece', 'unit' => 'kg', 'price_per_unit' => 95000],
            ['name' => 'Lacoste', 'unit' => 'kg', 'price_per_unit' => 110000],
        ];
        foreach ($materials as $material) {
            MasterMaterial::updateOrCreate(['name' => $material['name']], $material);
        }

        // 5. Clothing Categories
        $clothingCats = [
            ['name' => 'Kaos'],
            ['name' => 'Kemeja'],
            ['name' => 'Jaket'],
            ['name' => 'Seragam'],
            ['name' => 'Wangky (Polo)'],
        ];
        foreach ($clothingCats as $cat) {
            MasterClothingCategory::updateOrCreate(['name' => $cat['name']], $cat);
        }

        // 6. Tracking Statuses
        $statuses = [
            ['sort_order' => 1, 'code' => 'order_received', 'label' => 'Pesanan Diterima', 'group' => 'penerimaan', 'requires_payment' => false, 'has_sub_type' => false],
            ['sort_order' => 2, 'code' => 'material_order_pending', 'label' => 'Order Bahan - Belum Ready', 'group' => 'persiapan', 'requires_payment' => false, 'has_sub_type' => false],
            ['sort_order' => 3, 'code' => 'material_order_ready', 'label' => 'Order Bahan - Ready', 'group' => 'persiapan', 'requires_payment' => false, 'has_sub_type' => false],
            ['sort_order' => 4, 'code' => 'fabric_cutting', 'label' => 'Pemotongan Kain', 'group' => 'persiapan', 'requires_payment' => false, 'has_sub_type' => false],
            ['sort_order' => 5, 'code' => 'production', 'label' => 'Produksi', 'group' => 'produksi', 'requires_payment' => false, 'has_sub_type' => true],
            ['sort_order' => 6, 'code' => 'button_installation', 'label' => 'Pemasangan Kancing', 'group' => 'produksi', 'requires_payment' => false, 'has_sub_type' => false],
            ['sort_order' => 7, 'code' => 'qc', 'label' => 'Quality Control', 'group' => 'finishing', 'requires_payment' => false, 'has_sub_type' => false],
            ['sort_order' => 8, 'code' => 'ironing', 'label' => 'Setrika', 'group' => 'finishing', 'requires_payment' => false, 'has_sub_type' => false],
            ['sort_order' => 9, 'code' => 'packing', 'label' => 'Packing', 'group' => 'finishing', 'requires_payment' => false, 'has_sub_type' => false],
            ['sort_order' => 10, 'code' => 'shipping', 'label' => 'Pengiriman', 'group' => 'pengiriman', 'requires_payment' => true, 'has_sub_type' => false],
        ];
        foreach ($statuses as $status) {
            MasterTrackingStatus::updateOrCreate(['code' => $status['code']], $status);
        }

        // 7. Tracking Flow Rules (Sequential)
        $flow = [
            null => 'order_received',
            'order_received' => 'material_order_pending',
            'material_order_pending' => 'material_order_ready',
            'material_order_ready' => 'fabric_cutting',
            'fabric_cutting' => 'production',
            'production' => 'button_installation',
            'button_installation' => 'qc',
            'qc' => 'ironing',
            'ironing' => 'packing',
            'packing' => 'shipping',
        ];

        foreach ($flow as $fromCode => $toCode) {
            $fromId = $fromCode ? MasterTrackingStatus::where('code', $fromCode)->value('id') : null;
            $toId = MasterTrackingStatus::where('code', $toCode)->value('id');

            TrackingFlowRule::updateOrCreate([
                'from_status_id' => $fromId,
                'to_status_id' => $toId,
            ]);
        }
        
        // 8. Sample Material Usage Estimates for Cotton Combed 30s
        $cotton = MasterMaterial::where('name', 'Cotton Combed 30s')->first();
        if ($cotton) {
            $sizes = MasterSize::all();
            $estimates = [
                'XS' => 0.25,
                'S' => 0.28,
                'M' => 0.32,
                'L' => 0.35,
                'XL' => 0.38,
                'XXL' => 0.42,
                'XXXL' => 0.46,
            ];

            foreach ($sizes as $size) {
                if (isset($estimates[$size->code])) {
                    MaterialUsageEstimate::updateOrCreate([
                        'material_id' => $cotton->id,
                        'size_id' => $size->id,
                    ], [
                        'estimated_usage' => $estimates[$size->code],
                    ]);
                }
            }
        }
    }
}
