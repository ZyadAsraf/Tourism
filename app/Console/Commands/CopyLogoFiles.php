<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class CopyLogoFiles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'logo:copy {source? : Source path of the logo file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy logo files to all possible locations';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Get the source file path from the argument or use the default
        $sourcePath = $this->argument('source') ?? base_path('public/images/massar-logo.png');
        
        // Check if the source file exists
        if (!File::exists($sourcePath)) {
            $this->error("Source file not found at: $sourcePath");
            
            // Try to find the logo file in other locations
            $possibleSources = [
                base_path('public/images/massar-logo.png'),
                base_path('resources/images/massar-logo.png'),
                storage_path('app/public/images/massar-logo.png'),
                base_path('massar-logo.png'),
            ];
            
            foreach ($possibleSources as $path) {
                if (File::exists($path)) {
                    $sourcePath = $path;
                    $this->info("Found logo at: $sourcePath");
                    break;
                }
            }
            
            if (!File::exists($sourcePath)) {
                $this->error("Could not find the logo file in any known location.");
                return 1;
            }
        }
        
        // Destination paths
        $destinationPaths = [
            public_path('images/massar-logo.png'),
            public_path('massar-logo.png'),
            storage_path('app/public/images/massar-logo.png'),
            base_path('public/images/massar-logo.png'),
        ];
        
        // Create directories if they don't exist and copy the file
        foreach ($destinationPaths as $path) {
            $directory = dirname($path);
            if (!File::isDirectory($directory)) {
                File::makeDirectory($directory, 0755, true);
                $this->info("Created directory: $directory");
            }
            
            // Copy the file
            try {
                File::copy($sourcePath, $path, true);
                $this->info("Copied logo to: $path");
                
                // Set permissions
                chmod($path, 0644);
                $this->info("Set permissions for: $path");
            } catch (\Exception $e) {
                $this->error("Failed to copy to $path: " . $e->getMessage());
                Log::error("Failed to copy logo to $path: " . $e->getMessage());
            }
        }
        
        $this->info('Logo files copied successfully!');
        $this->info('Please run "php artisan storage:link" if you haven\'t already to create the symbolic link for storage.');
        
        return 0;
    }
}

