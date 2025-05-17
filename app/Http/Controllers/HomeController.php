<?php

namespace App\Http\Controllers;

use App\Models\Attraction;
use App\Models\Category;
use App\Models\Banner;
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
        
        foreach ($attractions as $attraction) {
            $slug = Str::slug($attraction->AttractionName);
            
            $featured[] = [
                'id' => $attraction->id,
                'title' => $attraction->AttractionName,
                'slug' => $slug,
                'price' => $attraction->EntryFee,
                'rating' => rand(4, 5) . '.' . rand(0, 9), // Generate random rating for now
                'reviewCount' => rand(1000, 10000), // Generate random review count for now
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

        // Get latest 6 articles
        $articles = Article::with('admin')->latest()->take(6)->get();

        return view('home', [
            'attractions' => $attractions,
            'featured' => $featured,
            'categories' => $formattedCategories,
            'banners' => $banners,
            'user' => $user,
            'articles' => $articles
        ]);
    }
}
