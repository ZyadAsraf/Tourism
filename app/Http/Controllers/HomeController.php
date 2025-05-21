<?php

namespace App\Http\Controllers;

use App\Models\Attraction;
use App\Models\Category;
use App\Models\Banner;
use App\Models\Itinerary;
use App\Models\User;
use App\Models\Article;
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
        
        // Get most liked public itineraries
        $mostLikedItineraries = Itinerary::with(['type', 'user', 'items.attraction'])
            ->where('public', true)
            ->orderBy('likes', 'desc')
            ->take(3)
            ->get();
            
        // Process itineraries to include first image and stats
        $attractionController = new AttractionController();
        $allAttractions = $attractionController->getAttractions();
        
        $itineraryController = new ItineraryController();
        foreach ($mostLikedItineraries as $itinerary) {
            $itinerary->groupedItems = $itineraryController->getItineraryItemsByDay($itinerary, $allAttractions);
            $itinerary->stats = $itineraryController->calculateItineraryStats($itinerary->groupedItems, $allAttractions);
        }
        
        // Get admin/official itineraries
        // Find users with the 'admin' role (this will depend on your user roles structure)
        // For now, let's get itineraries created by users with IDs 1 or 2 (assuming these are admin users)
        $adminIds = ['05e740fd-8714-459e-b1d1-095266338a12', 2]; // This should be replaced with actual admin ID detection
        
        $officialItineraries = Itinerary::with(['type', 'user', 'items.attraction'])
            ->whereIn('user_id', $adminIds)
            ->where('public', true)
            ->latest()
            ->take(3)
            ->get();
            
        // Process official itineraries too
        foreach ($officialItineraries as $itinerary) {
            $itinerary->groupedItems = $itineraryController->getItineraryItemsByDay($itinerary, $allAttractions);
            $itinerary->stats = $itineraryController->calculateItineraryStats($itinerary->groupedItems, $allAttractions);
        }
        
        // Get most liked public itineraries
        $mostLikedItineraries = Itinerary::with(['type', 'user', 'items.attraction'])
            ->where('public', true)
            ->orderBy('likes', 'desc')
            ->take(3)
            ->get();
            
        // Process itineraries to include first image and stats
        $attractionController = new AttractionController();
        $allAttractions = $attractionController->getAttractions();
        
        $itineraryController = new ItineraryController();
        foreach ($mostLikedItineraries as $itinerary) {
            $itinerary->groupedItems = $itineraryController->getItineraryItemsByDay($itinerary, $allAttractions);
            $itinerary->stats = $itineraryController->calculateItineraryStats($itinerary->groupedItems, $allAttractions);
        }
        
        // Get admin/official itineraries
        // Find users with the 'admin' role (this will depend on your user roles structure)
        // For now, let's get itineraries created by users with IDs 1 or 2 (assuming these are admin users)
        $adminIds = ['05e740fd-8714-459e-b1d1-095266338a12', 2]; // This should be replaced with actual admin ID detection
        
        $officialItineraries = Itinerary::with(['type', 'user', 'items.attraction'])
            ->whereIn('user_id', $adminIds)
            ->where('public', true)
            ->latest()
            ->take(3)
            ->get();
            
        // Process official itineraries too
        foreach ($officialItineraries as $itinerary) {
            $itinerary->groupedItems = $itineraryController->getItineraryItemsByDay($itinerary, $allAttractions);
            $itinerary->stats = $itineraryController->calculateItineraryStats($itinerary->groupedItems, $allAttractions);
        }
        
        // Get authenticated user data (if logged in)
        $user = Auth::user();

        // Get latest 6 articles
        $articles = Article::with('admin')->latest()->take(6)->get();

        return view('home', [
            'attractions' => $attractions,
            'featured' => $featured,
            'categories' => $formattedCategories,
            'banners' => $banners,
            'user' => $user,
            'mostLikedItineraries' => $mostLikedItineraries,
            'officialItineraries' => $officialItineraries,
            'articles' => $articles
        ]);
    }
}
