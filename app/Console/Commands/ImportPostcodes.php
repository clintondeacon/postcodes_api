<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use League\Csv\Reader;
use App\Models\Postcode;

class ImportPostcodes extends Command
{
    protected $signature = 'import:postcodes';
    protected $description = 'Download, extract, and import postcodes into the database';

    public function handle()
    {
        ini_set('memory_limit', '2048M');

        $zipUrl = 'https://parlvid.mysociety.org/os/ONSPD/2022-11.zip';
        $zipPath = storage_path('onspd.zip'); // Ensure the path matches where we save the file
        $extractPath = storage_path('onspd_extracted');
        $csvDirectory = $extractPath . '/Data/multi_csv';

        if(!is_dir($extractPath)) {
            // Download the ZIP file
            $this->info("Downloading ZIP file...");
            $response = Http::get($zipUrl);

            if ($response->failed()) {
                $this->error("Failed to download ZIP file.");
                return 1;
            }

            // Save the file where $zipPath expects it
            file_put_contents($zipPath, $response->body());

            // Ensure the file exists before proceeding
            if (!file_exists($zipPath)) {
                $this->error("ZIP file not found at: $zipPath");
                return 1;
            }

            // Extract ZIP file
            $this->info("Extracting ZIP file...");
            $zip = new ZipArchive;
            $res = $zip->open($zipPath);

            if ($res !== true) {
                $this->error("Failed to open ZIP file. Error code: " . $res);
                return 1;
            }

            if (!$zip->extractTo($extractPath)) {
                $this->error("Extraction failed. Ensure directory exists and is writable.");
                return 1;
            }

            $zip->close();
            $this->info("ZIP extraction successful!");

        }

        // Process CSV files
        if (!is_dir($csvDirectory)) {
            $this->error("CSV directory not found: $csvDirectory");
            return 1;
        }

        $files = glob("$csvDirectory/*.csv");

        if (!$files) {
            $this->error("No CSV files found in $csvDirectory");
            return 1;
        }

        $this->info("Processing CSV files...");

        foreach ($files as $file) {
            $this->info("Reading file: " . basename($file));

            // Read CSV file
            $csv = Reader::createFromPath($file, 'r');
            $csv->setHeaderOffset(0); // The first row contains headers

            foreach ($csv->getRecords() as $record) {
                $pcd = $record['pcd'] ?? null;
                $lat = $record['lat'] ?? null;
                $long = $record['long'] ?? null;

                // Validate and insert/update the record
                if ($pcd && is_numeric($lat) && is_numeric($long)) {
                    Postcode::updateOrCreate(
                        ['pcd' => $pcd],
                        ['lat' => $lat, 'long' => $long]
                    );
                }
            }
            // Lets just do one file for this example
            break;
        }

        $this->info("Postcode import completed.");
        return 0;
    }
}
