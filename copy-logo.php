<?php

// This is a simple script to copy the logo file to all necessary locations
// Run this script from the command line: php copy-logo.php

$sourcePath = __DIR__ . '/public/images/massar-logo.png';

// Check if the source file exists
if (!file_exists($sourcePath)) {
    echo "Source file not found at: $sourcePath\n";
    
    // Try to find the logo file in other locations
    $possibleSources = [
        __DIR__ . '/public/images/massar-logo.png',
        __DIR__ . '/resources/images/massar-logo.png',
        __DIR__ . '/storage/app/public/images/massar-logo.png',
        __DIR__ . '/massar-logo.png',
    ];
    
    foreach ($possibleSources as $path) {
        if (file_exists($path)) {
            $sourcePath = $path;
            echo "Found logo at: $sourcePath\n";
            break;
        }
    }
    
    if (!file_exists($sourcePath)) {
        echo "Could not find the logo file in any known location.\n";
        echo "Please place the massar-logo.png file in the public/images directory.\n";
        exit(1);
    }
}

// Destination paths
$destinationPaths = [
    __DIR__ . '/public/images/massar-logo.png',
    __DIR__ . '/public/massar-logo.png',
    __DIR__ . '/storage/app/public/images/massar-logo.png',
];

// Create directories if they don't exist and copy the file
foreach ($destinationPaths as $path) {
    $directory = dirname($path);
    if (!is_dir($directory)) {
        mkdir($directory, 0755, true);
        echo "Created directory: $directory\n";
    }
    
    // Copy the file
    try {
        copy($sourcePath, $path);
        echo "Copied logo to: $path\n";
        
        // Set permissions
        chmod($path, 0644);
        echo "Set permissions for: $path\n";
    } catch (Exception $e) {
        echo "Failed to copy to $path: " . $e->getMessage() . "\n";
    }
}

echo "Logo files copied successfully!\n";
echo "Please run 'php artisan storage:link' if you haven't already to create the symbolic link for storage.\n";

