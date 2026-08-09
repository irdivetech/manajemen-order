<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MasterGender;
use App\Models\MasterSizeCategory;
use App\Models\MasterSize;
use App\Models\MasterMaterial;
use App\Models\MasterClothingCategory;
use App\Models\MaterialUsageEstimate;
use App\Models\MasterTrackingStatus;
use Illuminate\Support\Str;

class MasterDataController extends Controller
{
    protected function getConfig($type)
    {
        $configs = [
            'genders' => [
                'model' => MasterGender::class,
                'title' => 'Master Gender',
                'fields' => [
                    'code' => ['label' => 'Kode', 'type' => 'text', 'rules' => 'required|string|max:255|unique:master_genders,code'],
                    'label' => ['label' => 'Label', 'type' => 'text', 'rules' => 'required|string|max:255'],
                    'is_active' => ['label' => 'Aktif', 'type' => 'boolean', 'rules' => 'boolean']
                ],
                'relations' => []
            ],
            'size-categories' => [
                'model' => MasterSizeCategory::class,
                'title' => 'Master Kategori Ukuran',
                'fields' => [
                    'name' => ['label' => 'Nama Kategori', 'type' => 'text', 'rules' => 'required|string|max:255'],
                    'sort_order' => ['label' => 'Urutan', 'type' => 'number', 'rules' => 'required|integer'],
                    'is_active' => ['label' => 'Aktif', 'type' => 'boolean', 'rules' => 'boolean']
                ],
                'relations' => []
            ],
            'sizes' => [
                'model' => MasterSize::class,
                'title' => 'Master Ukuran',
                'fields' => [
                    'size_type' => ['label' => 'Jenis Size', 'type' => 'select', 'options' => ['standard' => 'Standard', 'big' => 'Big'], 'rules' => 'required|in:standard,big'],
                    'code' => ['label' => 'Kode Ukuran', 'type' => 'text', 'rules' => 'required|string|max:255'],
                    'label' => ['label' => 'Label Tampilan', 'type' => 'text', 'rules' => 'required|string|max:255'],
                    'sort_order' => ['label' => 'Urutan', 'type' => 'number', 'rules' => 'required|integer'],
                    'is_active' => ['label' => 'Aktif', 'type' => 'boolean', 'rules' => 'boolean']
                ],
                'relations' => []
            ],
            'materials' => [
                'model' => MasterMaterial::class,
                'title' => 'Master Bahan',
                'fields' => [
                    'name' => ['label' => 'Nama Bahan', 'type' => 'text', 'rules' => 'required|string|max:255'],
                    'unit' => ['label' => 'Satuan', 'type' => 'text', 'rules' => 'required|string|max:255'],
                    'price_per_unit' => ['label' => 'Harga per Satuan', 'type' => 'number', 'step' => '0.01', 'rules' => 'required|numeric|min:0'],
                    'is_active' => ['label' => 'Aktif', 'type' => 'boolean', 'rules' => 'boolean']
                ],
                'relations' => []
            ],
            'clothing-categories' => [
                'model' => MasterClothingCategory::class,
                'title' => 'Master Kategori Baju',
                'fields' => [
                    'name' => ['label' => 'Nama Kategori', 'type' => 'text', 'rules' => 'required|string|max:255'],
                    'is_active' => ['label' => 'Aktif', 'type' => 'boolean', 'rules' => 'boolean']
                ],
                'relations' => []
            ],
            'usage-estimates' => [
                'model' => MaterialUsageEstimate::class,
                'title' => 'Estimasi Penggunaan Bahan',
                'fields' => [
                    'material_id' => ['label' => 'Bahan', 'type' => 'select_model', 'model' => MasterMaterial::class, 'display' => 'name', 'rules' => 'required|exists:master_materials,id'],
                    'size_id' => ['label' => 'Ukuran', 'type' => 'select_model', 'model' => MasterSize::class, 'display' => 'label', 'rules' => 'required|exists:master_sizes,id'],
                    'estimated_usage' => ['label' => 'Estimasi Penggunaan', 'type' => 'number', 'step' => '0.0001', 'rules' => 'required|numeric|min:0']
                ],
                'relations' => ['material', 'size']
            ],
            'tracking-statuses' => [
                'model' => MasterTrackingStatus::class,
                'title' => 'Master Status Tracking',
                'fields' => [
                    'code' => ['label' => 'Kode', 'type' => 'text', 'rules' => 'required|string|unique:master_tracking_statuses,code'],
                    'label' => ['label' => 'Label', 'type' => 'text', 'rules' => 'required|string|max:255'],
                    'group' => ['label' => 'Grup', 'type' => 'text', 'rules' => 'nullable|string|max:255'],
                    'sort_order' => ['label' => 'Urutan', 'type' => 'number', 'rules' => 'required|integer'],
                    'requires_payment' => ['label' => 'Butuh Lunas', 'type' => 'boolean', 'rules' => 'boolean'],
                    'has_sub_type' => ['label' => 'Punya Sub-Tipe (Produksi)', 'type' => 'boolean', 'rules' => 'boolean'],
                    'is_active' => ['label' => 'Aktif', 'type' => 'boolean', 'rules' => 'boolean']
                ],
                'relations' => []
            ],
        ];

        if (!isset($configs[$type])) {
            abort(404);
        }

        return $configs[$type];
    }

    public function index($type)
    {
        $config = $this->getConfig($type);
        $query = $config['model']::query();
        if (!empty($config['relations'])) {
            $query->with($config['relations']);
        }
        $data = $query->get();

        return view('master-data.index', compact('type', 'config', 'data'));
    }

    public function create($type)
    {
        $config = $this->getConfig($type);
        
        // Load data for select_model fields
        $selectData = [];
        foreach ($config['fields'] as $key => $field) {
            if ($field['type'] === 'select_model') {
                $selectData[$key] = $field['model']::all();
            }
        }

        return view('master-data.form', compact('type', 'config', 'selectData'));
    }

    public function store(Request $request, $type)
    {
        $config = $this->getConfig($type);
        
        $rules = [];
        foreach ($config['fields'] as $key => $field) {
            $rules[$key] = $field['rules'];
        }

        // Special handling for unique constraint in usage-estimates
        if ($type === 'usage-estimates') {
            $rules['size_id'] .= '|unique:material_usage_estimates,size_id,NULL,id,material_id,' . $request->material_id;
        }

        $validated = $request->validate($rules);
        
        // Handle booleans (checkboxes don't send false if unchecked)
        foreach ($config['fields'] as $key => $field) {
            if ($field['type'] === 'boolean') {
                $validated[$key] = $request->has($key);
            }
        }

        $config['model']::create($validated);

        return redirect()->route('master-data.index', $type)->with('success', 'Data berhasil ditambahkan.');
    }

    public function edit($type, $id)
    {
        $config = $this->getConfig($type);
        $data = $config['model']::findOrFail($id);
        
        // Load data for select_model fields
        $selectData = [];
        foreach ($config['fields'] as $key => $field) {
            if ($field['type'] === 'select_model') {
                $selectData[$key] = $field['model']::all();
            }
        }

        return view('master-data.form', compact('type', 'config', 'data', 'selectData'));
    }

    public function update(Request $request, $type, $id)
    {
        $config = $this->getConfig($type);
        $data = $config['model']::findOrFail($id);
        
        $rules = [];
        foreach ($config['fields'] as $key => $field) {
            $rule = $field['rules'];
            // Adjust unique rules for update
            if (Str::contains($rule, 'unique:')) {
                // simple replace for standard unique
                $rule = preg_replace('/unique:([^,]+),([^,\|]+)/', 'unique:$1,$2,' . $id, $rule);
            }
            $rules[$key] = $rule;
        }

        // Special handling for unique constraint in usage-estimates
        if ($type === 'usage-estimates') {
            $rules['size_id'] = 'required|exists:master_sizes,id|unique:material_usage_estimates,size_id,' . $id . ',id,material_id,' . $request->material_id;
        }

        $validated = $request->validate($rules);

        // Handle booleans (checkboxes don't send false if unchecked)
        foreach ($config['fields'] as $key => $field) {
            if ($field['type'] === 'boolean') {
                $validated[$key] = $request->has($key);
            }
        }

        $data->update($validated);

        return redirect()->route('master-data.index', $type)->with('success', 'Data berhasil diperbarui.');
    }

    public function destroy($type, $id)
    {
        $config = $this->getConfig($type);
        $data = $config['model']::findOrFail($id);
        
        try {
            $data->delete();
            return redirect()->route('master-data.index', $type)->with('success', 'Data berhasil dihapus.');
        } catch (\Exception $e) {
            return redirect()->route('master-data.index', $type)->with('error', 'Data tidak dapat dihapus karena sedang digunakan.');
        }
    }
}
