<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;

class SyncRegions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'regions:sync';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync Indonesia regions (Provinces, Cities, Districts) from EMSIFA API to local JSON files for offline use.';

    private string $baseUrl = 'https://emsifa.github.io/api-wilayah-indonesia/api';

    /**
     * Make an HTTP request with SSL bypass (for Windows compatibility) and retry logic.
     */
    private function fetchJson(string $url): ?array
    {
        try {
            $response = Http::withoutVerifying()
                ->timeout(30)
                ->retry(3, 1000)
                ->get($url);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Exception $e) {
            $this->warn("  Request failed: {$url} — {$e->getMessage()}");
        }

        return null;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting Region Data Sync...');
        $this->info('Source: ' . $this->baseUrl);

        Storage::makeDirectory('regions/cities');
        Storage::makeDirectory('regions/districts');

        // 1. Fetch Provinces
        $this->info('Fetching Provinces...');
        $provinces = $this->fetchJson("{$this->baseUrl}/provinces.json");

        if (!$provinces) {
            $this->error('Failed to fetch provinces. Please check your internet connection.');
            return Command::FAILURE;
        }

        Storage::put('regions/provinces.json', json_encode($provinces, JSON_PRETTY_PRINT));
        $this->info('Saved ' . count($provinces) . ' provinces.');

        // 2. Fetch Cities for each Province
        $this->info('Fetching Cities and Districts...');
        $bar = $this->output->createProgressBar(count($provinces));
        $bar->start();

        $totalCities = 0;
        $totalDistricts = 0;

        foreach ($provinces as $province) {
            $provinceId = $province['id'];

            $cities = $this->fetchJson("{$this->baseUrl}/regencies/{$provinceId}.json");
            if ($cities) {
                Storage::put("regions/cities/{$provinceId}.json", json_encode($cities, JSON_PRETTY_PRINT));
                $totalCities += count($cities);

                // 3. Fetch Districts for each City
                foreach ($cities as $city) {
                    $cityId = $city['id'];
                    $districts = $this->fetchJson("{$this->baseUrl}/districts/{$cityId}.json");

                    if ($districts) {
                        Storage::put("regions/districts/{$cityId}.json", json_encode($districts, JSON_PRETTY_PRINT));
                        $totalDistricts += count($districts);
                    }
                }
            }
            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("Sync complete!");
        $this->info("  Provinces : " . count($provinces));
        $this->info("  Cities    : {$totalCities}");
        $this->info("  Districts : {$totalDistricts}");
        $this->info("Files stored in: storage/app/regions/");

        return Command::SUCCESS;
    }
}

