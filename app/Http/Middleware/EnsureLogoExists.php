<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class EnsureLogoExists
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $logoPath = public_path('images/massar-logo.png');
        
        // If the logo doesn't exist, try to copy it from the uploaded image
        if (!File::exists($logoPath)) {
            Log::info('Logo not found, attempting to create it');
            
            // Check if we have the logo in the storage
            $storagePath = storage_path('app/public/images/massar-logo.png');
            if (File::exists($storagePath)) {
                // Create directory if it doesn't exist
                if (!File::isDirectory(public_path('images'))) {
                    File::makeDirectory(public_path('images'), 0755, true);
                }
                
                // Copy the file
                File::copy($storagePath, $logoPath);
                Log::info('Logo copied from storage to public path');
            } else {
                Log::warning('Logo not found in storage either');
            }
        }
        
        return $next($request);
    }
}

