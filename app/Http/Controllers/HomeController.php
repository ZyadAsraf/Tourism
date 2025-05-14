<?php

namespace App\Http\Controllers;

use App\Models\Attraction;
use App\Models\Category;
use App\Models\Banner;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        // Get featured attractions (limit to 6)
        $attractions = Attraction::where('Status', 'Available')
            ->with(['governorate', 'categories', 'admin'])
            ->take(6)
            ->get();
        
        $featured = [];

        foreach ($attractions as $attraction) {
            // Process AttractionName
            $rawName = $attraction->AttractionName;
            if (is_string($rawName)) {
                $decodedName = json_decode($rawName, true); // Decode only if it's a JSON string
                $rawName = $decodedName ?? $rawName; // Fallback to the original if decoding fails
            } elseif (is_array($rawName)) {
                $rawName = reset($rawName); // If it's an array, just take the first value
            }

            $slug = Str::slug($rawName);

            // Process Description
            $description = $attraction->Description;
            if (is_string($description)) {
                // Decode the string if it's JSON
                $description = json_decode($description, true) ?? $description;
            } elseif (is_array($description)) {
                // Convert array to string (adjust this based on how you want to present the description)
                $description = implode(' ', $description); // Converts array to a space-separated string
            }

            $featured[] = [
                'id' => $attraction->id,
                'title' => $rawName,
                'slug' => $slug,
                'price' => $attraction->EntryFee,
                'rating' => rand(4, 5) . '.' . rand(0, 9), // Generate random rating for now
                'reviewCount' => rand(1000, 10000), // Generate random review count for now
                'description' => $description, // Pass the description as a string
                'image' => $attraction->Img ? '/storage/' . $attraction->Img : '/images/placeholder.jpg',
                'location' => $attraction->City ?? $attraction->governorate->Name ?? 'Egypt',
                'duration' => '4 hours', // Default duration
            ];

        }

        // Get categories
        $categories = Category::pluck('Name', 'id')->toArray();
        $formattedCategories = [];
        
        foreach ($categories as $id => $name) {
            $slug = Str::slug($name);
            $formattedCategories[$slug] = $name;
        }
        
        // Get banners for homepage if they exist
        $banners = Banner::where('is_visible', true)
            ->whereDate('start_date', '<=', now())
            ->whereDate('end_date', '>=', now())
            ->orderBy('sort')
            ->get();
        
        // Get authenticated user data (if logged in)
        $user = Auth::user();

        return view('home', [
            'attractions' => $attractions,
            'featured' => $featured,
            'categories' => $formattedCategories,
            'banners' => $banners,
            'user' => $user
        ]);
    }
}
