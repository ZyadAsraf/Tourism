<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attraction;
use App\Models\Category;
use App\Models\Review;
use Illuminate\Support\Str;

class AttractionController extends Controller
{
// Get attractions from the database
public function getAttractions(Request $request = null)
{
    // Fetch attractions from the database that are marked as Available
    $query = Attraction::where('Status', 'Available')
        ->with(['governorate', 'categories', 'admin']);
    
    // Apply filters if provided
    if ($request) {
        // Filter by categories
        if ($request->has('categories') && !empty($request->categories)) {
            $categoryNames = [];
            $categories = Category::all();
            
            // Convert slugs to actual category names
            foreach ($request->categories as $slug) {
                foreach ($categories as $category) {
                    if (Str::slug($category->Name) === $slug) {
                        $categoryNames[] = $category->Name;
                    }
                }
            }
            
            if (!empty($categoryNames)) {
                $query->whereHas('categories', function($q) use ($categoryNames) {
                    $q->whereIn('Name', $categoryNames);
                });
            }
        }
        
        // Filter by price range
        if ($request->has('min_price') && is_numeric($request->min_price)) {
            $query->where('EntryFee', '>=', $request->min_price);
        }
        
        if ($request->has('max_price') && is_numeric($request->max_price)) {
            $query->where('EntryFee', '<=', $request->max_price);
        }
    }
    
    $attractions = $query->get();
    
    $formattedAttractions = [];
    
    // Map duration values to attraction IDs (in a real app, this would come from the database)
    $durationMap = [
        // Assign durations to attractions based on ID
        // This is a placeholder - in a real app, you'd have this in the database
        1 => 'short',  // Less than 3 hours
        2 => 'medium', // 3-5 hours
        3 => 'full_day', // Full day
        4 => 'multi_day', // Multi-day
    ];
    
    // Map duration types to human-readable text
    $durationText = [
        'short' => 'Less than 3 hours',
        'medium' => '3-5 hours',
        'full_day' => 'Full day',
        'multi_day' => 'Multi-day'
    ];
    
    foreach ($attractions as $attraction) {
        $slug = Str::slug($attraction->AttractionName);
        
        // Assign a duration type based on attraction ID (or any other logic)
        // In a real app, this would come from the database
        $durationType = $durationMap[$attraction->id % 4 + 1] ?? 'medium';
        $duration = $durationText[$durationType];
        
        // Filter by duration if requested
        if ($request && $request->has('durations') && !empty($request->durations)) {
            if (!in_array($durationType, $request->durations)) {
                continue; // Skip this attraction as it doesn't match the duration filter
            }
        }
        
        $formattedAttractions[$slug] = [
            'id' => $attraction->id,
            'title' => $attraction->AttractionName,
            'slug' => $slug,
            'price' => $attraction->EntryFee,
            'rating' => rand(4, 5) . '.' . rand(0, 9), // Generate random rating for now
            'reviewCount' => rand(1000, 10000), // Generate random review count for now
            'description' => strip_tags(Str::markdown($attraction->Description)),
            'image' => $attraction->Img ? '/storage/' . $attraction->Img : '/images/placeholder.jpg',
            'gallery' => $attraction->Img ? ['/storage/' . $attraction->Img] : ['/images/placeholder.jpg'],
            'mapImage' => $attraction->LocationLink ?? '/images/map-placeholder.jpg',
            'category' => $attraction->categories->isNotEmpty() ? $attraction->categories->first()->Name : 'Attraction',
            'location' => $attraction->City ?? $attraction->governorate->Name ?? 'Egypt',
            'duration' => $duration,
            'durationType' => $durationType, // Store the duration type for filtering
            'included' => ['Entrance fees', 'Guide'],
            'notIncluded' => ['Transportation', 'Meals', 'Gratuities'],
            'featured' => true
        ];
    }
    
    // If no attractions found and we're not filtering, return a placeholder
    if (empty($formattedAttractions) && (!$request || 
        (!$request->has('categories') && !$request->has('durations') && 
         !$request->has('min_price') && !$request->has('max_price')))) {
        $formattedAttractions['sample-attraction'] = [
            'id' => 1,
            'title' => 'Sample Attraction',
            'slug' => 'sample-attraction',
            'price' => 100,
            'rating' => 4.5,
            'reviewCount' => 1000,
            'description' => 'This is a sample attraction. Please add real attractions through the admin panel.',
            'longDescription' => 'This is a sample attraction. Please add real attractions through the admin panel.',
            'image' => '/images/placeholder.jpg',
            'gallery' => ['/images/placeholder.jpg'],
            'mapImage' => '/images/map-placeholder.jpg',
            'category' => 'Attraction',
            'location' => 'Egypt',
            'duration' => 'Less than 3 hours',
            'durationType' => 'short',
            'included' => ['Entrance fees', 'Guide'],
            'notIncluded' => ['Transportation', 'Meals', 'Gratuities'],
            'featured' => true
        ];
    }
    
    return $formattedAttractions;
}

public function getCategories()
{
    $categories = Category::all();
    $formattedCategories = [];
    
    foreach ($categories as $category) {
        $slug = Str::slug($category->Name);
        $formattedCategories[$slug] = $category->Name;
    }
    
    // If no categories found, return default ones
    if (empty($formattedCategories)) {
        return [
            'historical' => 'Historical Sites',
            'nature' => 'Nature & Wildlife',
            'cultural' => 'Cultural Experiences',
            'adventure' => 'Adventure Activities'
        ];
    }
    
    return $formattedCategories;
}

public function home()
{
    $attractions = $this->getAttractions();
    $featured = array_filter($attractions, function($attraction) {
        return $attraction['featured'] === true;
    });
    
    return view('home', [
        'attractions' => $attractions,
        'featured' => $featured,
        'categories' => $this->getCategories()
    ]);
}

public function index(Request $request)
{
    return view('attraction.index', [
        'attractions' => $this->getAttractions($request),
        'categories' => $this->getCategories()
    ]);
}

public function show($slug)
{
    $attractions = $this->getAttractions();
    
    if (!isset($attractions[$slug])) {
        abort(404);
    }

    // Get related attractions in the same category
    $category = $attractions[$slug]['category'];
    $related = array_filter($attractions, function($item) use ($category, $slug) {
        return $item['category'] === $category && $item['slug'] !== $slug;
    });

    // Fetch reviews for this attraction
    $reviews = Review::where('attraction_id', $attractions[$slug]['id'])->latest()->get();

    return view('attraction.show', [
        'attraction' => $attractions[$slug],
        'related' => array_slice($related, 0, 3),
        'categories' => $this->getCategories(),
        'reviews' => $reviews, // pass the reviews to the view
    ]);
}


// Update the byCategory method to properly handle category slugs
public function byCategory($category)
{
    // Get all categories to map slug to name
    $allCategories = Category::all();
    $categoryName = null;
    
    // Find the category name from the slug
    foreach ($allCategories as $cat) {
        if (Str::slug($cat->Name) === $category) {
            $categoryName = $cat->Name;
            break;
        }
    }
    
    // If category name not found, use the slug
    if (!$categoryName) {
        $categoryName = $category;
    }
    
    // Create a request with the category filter
    $request = new Request();
    $request->merge(['categories' => [$category]]);
    
    // Get filtered attractions
    $attractions = $this->getAttractions($request);
    
    return view('attraction.category', [
        'attractions' => $attractions,
        'category' => $category,
        'categoryName' => $this->getCategories()[$category] ?? 'Unknown Category',
        'categories' => $this->getCategories()
    ]);
}

public function search(Request $request)
{
    $query = $request->input('query');
    $attractions = $this->getAttractions();
    
    if ($query) {
        $filtered = array_filter($attractions, function($attraction) use ($query) {
            return stripos($attraction['title'], $query) !== false || 
                   stripos($attraction['description'], $query) !== false ||
                   stripos($attraction['location'], $query) !== false;
        });
    } else {
        $filtered = $attractions;
    }
    
    return view('attraction.search', [
        'attractions' => $filtered,
        'query' => $query,
        'categories' => $this->getCategories()
    ]);
}




// public function processBooking(Request $request, $attraction)
// {
//     // In a real app, this would validate and save the booking and payment details to a database
    
//     // Validate payment details
//     $validated = $request->validate([
//         'payment_method' => 'required|string',
//         'date' => 'required|date',
//         'time' => 'required',
//         'guests' => 'required|integer|min:1',
//         // Add more validation rules for payment details based on the selected payment method
//         'card_number' => 'required_if:payment_method,credit_card',
//         'expiry_date' => 'required_if:payment_method,credit_card',
//         'cvv' => 'required_if:payment_method,credit_card',
//         'cardholder_name' => 'required_if:payment_method,credit_card',
//         'country' => 'required',
//         'address' => 'required',
//         'city' => 'required',
//         'postal_code' => 'required',
//         'terms' => 'required',
//     ]);
    
//     // Process payment (in a real app, this would integrate with a payment gateway)
//     // For now, just simulate a successful payment
    
//     // Redirect to confirmation page with success message
//     return redirect()->route('attractions.show', $attraction)->with('success', 'Your booking has been confirmed! Payment was successful. Please check your email for confirmation details.');
// }

public function reviews($slug)
{
    $attractions = $this->getAttractions();
    
    if (!isset($attractions[$slug])) {
        abort(404);
    }
    
    return view('attraction.review', [
        'attraction' => $attractions[$slug],
        'categories' => $this->getCategories()
    ]);
}


public function addReview(Request $request, $slug)
{
    // Validate the incoming review data
    $validated = $request->validate([
        'rating' => 'required|integer|min:1|max:5',
        'comment' => 'required|string|max:500',
    ]);

    // Find the attraction by matching the slug
    $attraction = Attraction::all()->first(function ($attraction) use ($slug) {
        return Str::slug($attraction->AttractionName) === $slug;
    });

    // If not found, abort with a 404
    if (!$attraction) {
        abort(404, 'Attraction not found.');
    }

    // Create a new review
    $review = new Review();
    $review->rating = $validated['rating'];
    $review->comment = $validated['comment'];
    $review->tourist_id = auth()->id(); // Assuming the user is authenticated
    $review->attraction_id = $attraction->id;

    // Save the review to the database
    $review->save();

    // Redirect back to the attraction page with a success message
    return redirect()->route('attractions.show', $slug)->with('success', 'Your review has been submitted successfully!');
}

}