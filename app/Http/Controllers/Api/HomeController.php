<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Attraction;
use App\Models\Category;
use Illuminate\Support\Str;
use App\Models\Banner;
use URL;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\AttractionController;

class HomeController extends Controller
{

    public function indexApi(Request $request)
{
    try {
        // Get featured attractions (limit to 6)
        $attractions = Attraction::where('Status', 'Available')
            ->with(['governorate', 'categories', 'admin'])
            ->take(6)
            ->get();
        
        $featured = [];
        
        // Get attraction IDs for batch review stats retrieval
        $attractionIds = $attractions->pluck('id')->toArray();
        
        // Use the AttractionController to get review stats for all attractions at once
        $attractionController = new AttractionController();
        $reviewStats = $attractionController->getMultipleAttractionReviewStats($attractionIds);
        
        foreach ($attractions as $attraction) {
            $slug = Str::slug($attraction->AttractionName);
            
            $featured[] = [
                'id' => $attraction->id,
                'title' => $attraction->AttractionName,
                'slug' => $slug,
                'price' => $attraction->EntryFee,
                'rating' => $reviewStats[$attraction->id]['average_rating'],
                'reviewCount' => $reviewStats[$attraction->id]['review_count'],
                'description' => strip_tags(Str::markdown($attraction->Description)),
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

        // Return data as JSON response
        return response()->json([
            'success' => true,
            'data' => [
                'attractions' => $featured,
                'categories' => $formattedCategories,
                'banners' => $banners,
                'user' => $user ? $user : null // Include user info if logged in
            ]
        ]);
    } catch (\Exception $e) {
        // Return error message if any exception occurs
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'trace' => $e->getTrace()
        ], 500);
    }
}

}