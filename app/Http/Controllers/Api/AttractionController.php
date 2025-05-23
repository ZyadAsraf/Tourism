<?php

namespace App\Http\Controllers\Api; 

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use App\Models\Review;
use App\Models\Attraction;
use App\Models\Category;
use Illuminate\Support\Str;
use URL;

class AttractionController extends Controller
{
// Get attractions from the database
public function getAttractions(Request $request = null)
{
    // Fetch attractions from the database that are marked as Available
    $query = Attraction::where('Status', 'Available')
        ->with(['governorate', 'categories', 'admin', 'images', 'images360']) // Eager load images
        ->select(['*']);
    
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

        // Get regular gallery images
        $galleryImages = [];
        if ($attraction->images) {
            foreach ($attraction->images as $image) {
                $galleryImages[] = '/storage/' . $image->filename;
            }
        }
        if (empty($galleryImages) && $attraction->Img) {
            $galleryImages[] = '/storage/' . $attraction->Img;
        } elseif (empty($galleryImages)) {
            $galleryImages[] = '/images/placeholder.jpg';
        }

        // Get 360° images
        $panoramaImages = [];
        if ($attraction->images360) {
            foreach ($attraction->images360 as $image360) {
                $panoramaImages[] = [
                    'url' => '/storage/' . $image360->filename,
                    'caption' => $image360->alt_text ?? $attraction->AttractionName
                ];
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
            'longDescription' => strip_tags(Str::markdown($attraction->Description)),
            'image' => $attraction->Img ? '/storage/' . $attraction->Img : ($galleryImages[0] ?? '/images/placeholder.jpg'),
            'gallery' => $galleryImages,
            'panorama_images' => $panoramaImages,
            'mapImage' => $attraction->LocationLink ?? url('/').'/images/map-placeholder.jpg',
            'category' => $attraction->categories->isNotEmpty() ? Str::slug($attraction->categories->first()->Name) : 'attraction',
            'categoryName' => $attraction->categories->isNotEmpty() ? $attraction->categories->first()->Name : 'Attraction',
            'location' => $attraction->City ?? $attraction->governorate->Name ?? 'Egypt',
            'duration' => $duration,
            'durationType' => $durationType, // Store the duration type for filtering
            'included' => ['Entrance fees', 'Guide'],
            'notIncluded' => ['Transportation', 'Meals', 'Gratuities'],
            'featured' => true // You might want to make this dynamic based on a DB field
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
            'panorama_images' => [],
            'mapImage' => URL::to('/images/map-placeholder.jpg'),
            'category' => 'attraction',
            'categoryName' => 'Attraction',
            'location' => 'Egypt',
            'duration' => 'Less than 3 hours',
            'durationType' => 'short',
            'included' => ['Entrance fees', 'Guide'],
            'notIncluded' => ['Transportation', 'Meals', 'Gratuities'],
            'featured' => true
        ];
    }
    
    return response()->json($formattedAttractions); // Return as a flat array
}

public function getCategories(): JsonResponse
{
    $categories = Category::all();
    $formattedCategories = [];

    foreach ($categories as $category) {
        $slug = Str::slug($category->Name);
        $formattedCategories[$slug] = $category->Name;
    }

    if (empty($formattedCategories)) {
        $formattedCategories = [
            'historical' => 'Historical Sites',
            'nature' => 'Nature & Wildlife',
            'cultural' => 'Cultural Experiences',
            'adventure' => 'Adventure Activities'
        ];
    }

    return response()->json([
        'success' => true,
        'data' => $formattedCategories
    ]);
}

public function homeApi()
{
    try {
        $attractionsResponse = $this->getAttractions(); // JsonResponse
        $attractions = $attractionsResponse->getData(true); // Convert to array

        $featured = array_filter($attractions, function ($attraction) {
            return isset($attraction['featured']) && $attraction['featured'] === true;
        });

        $categoriesResponse = $this->getCategories(); // JsonResponse
        $categories = $categoriesResponse->getData(true)['data'] ?? [];

        return response()->json([
            'success' => true,
            'data' => [
                'attractions' => $attractions,
                'featured' => array_values($featured),
                'categories' => $categories
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'trace' => $e->getTrace()
        ], 500);
    }
}

public function indexApi(Request $request): JsonResponse
{
    try {
        // Call methods and extract their data
        $attractionsResponse = $this->getAttractions($request);
        $attractions = $attractionsResponse->getData(true); // Converts JsonResponse to array

        $categoriesResponse = $this->getCategories();
        $categories = $categoriesResponse->getData(true)['data'] ?? [];

        return response()->json([
            'success' => true,
            'data' => [
                'attractions' => $attractions,
                'categories' => $categories
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'trace' => $e->getTrace()
        ], 500);
    }
}

public function showApi($slug): JsonResponse
{
    try {
        // Get attractions and convert to array
        $attractionsResponse = $this->getAttractions();
        $attractions = $attractionsResponse->getData(true);

        // Check if the attraction with given slug exists
        if (!isset($attractions[$slug])) {
            return response()->json([
                'success' => false,
                'message' => 'Attraction not found.'
            ], 404);
        }

        $attraction = $attractions[$slug];
        $category = $attraction['category'];

        $attractionModel = Attraction::find($attraction['id']);

        $regularImages = $attractionModel->images()->get();
        $galleryImages = [];
        
        foreach ($regularImages as $image) {
            $galleryImages[] = '/storage/' . $image->filename;
        }
        
        // Add existing gallery images if available
        if (!empty($galleryImages)) {
            $attraction['gallery'] = $galleryImages;
        }
        
        // Get 360° images
        $images360 = $attractionModel->images360()->get();
        $panoramaImages = [];
        
        foreach ($images360 as $image) {
            $panoramaImages[] = [
                'url' => '/storage/' . $image->filename,
                'caption' => $image->alt_text ?? $attraction['title']
            ];
        }
        
        $attraction['panorama_images'] = $panoramaImages;

        // Get related attractions in the same category
        $related = array_filter($attractions, function ($item) use ($category, $slug) {
            return $item['category'] === $category && $item['slug'] !== $slug;
        });

        // Limit to 3 related
        $related = array_slice($related, 0, 3);

        // Get categories
        $categoriesResponse = $this->getCategories();
        $categories = $categoriesResponse->getData(true)['data'] ?? [];

        return response()->json([
            'success' => true,
            'data' => [
                'attraction' => $attraction,
                'related' => array_values($related),
                'categories' => $categories
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'trace' => $e->getTrace()
        ], 500);
    }
}


// Update the byCategory method to properly handle category slugs
public function byCategoryApi($category): JsonResponse
{
    try {
        // Get all categories to map slug to name
        $allCategories = Category::all();
        $categoryName = null;

        foreach ($allCategories as $cat) {
            if (Str::slug($cat->Name) === $category) {
                $categoryName = $cat->Name;
                break;
            }
        }

        // Fallback if not found
        if (!$categoryName) {
            $categoryName = $category;
        }

        // Create request with category filter
        $request = new Request();
        $request->merge(['categories' => [$category]]);

        // Fetch filtered attractions
        $attractionsResponse = $this->getAttractions($request);
        $attractions = $attractionsResponse->getData(true);

        // Fetch all categories (key => value format)
        $categoriesResponse = $this->getCategories();
        $categories = $categoriesResponse->getData(true)['data'] ?? [];

        return response()->json([
            'success' => true,
            'data' => [
                'attractions' => $attractions,
                'category' => $category,
                'categoryName' => $categories[$category] ?? 'Unknown Category',
                'categories' => $categories
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'trace' => $e->getTrace()
        ], 500);
    }
}


public function searchApi(Request $request): JsonResponse
{
    try {
        $query = $request->input('query');
        $attractionsResponse = $this->getAttractions();
        $attractions = $attractionsResponse->getData(true);

        if ($query) {
            $filtered = array_filter($attractions, function ($attraction) use ($query) {
                return stripos($attraction['title'], $query) !== false ||
                       stripos($attraction['description'], $query) !== false ||
                       stripos($attraction['location'], $query) !== false;
            });
        } else {
            $filtered = $attractions;
        }

        $categoriesResponse = $this->getCategories();
        $categories = $categoriesResponse->getData(true)['data'] ?? [];

        return response()->json([
            'success' => true,
            'data' => [
                'attractions' => array_values($filtered),
                'query' => $query,
                'categories' => $categories
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'trace' => $e->getTrace()
        ], 500);
    }
}

public function bookingFormApi($attraction): JsonResponse
{
    try {
        $attractionsResponse = $this->getAttractions();
        $attractions = $attractionsResponse->getData(true);

        if (!isset($attractions[$attraction])) {
            return response()->json([
                'success' => false,
                'message' => 'Attraction not found.'
            ], 404);
        }

        $categoriesResponse = $this->getCategories();
        $categories = $categoriesResponse->getData(true)['data'] ?? [];

        return response()->json([
            'success' => true,
            'data' => [
                'attraction' => $attractions[$attraction],
                'categories' => $categories
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'trace' => $e->getTrace()
        ], 500);
    }
}


public function paymentFormApi(Request $request, $attraction): JsonResponse
{
    try {
        $attractionsResponse = $this->getAttractions();
        $attractions = $attractionsResponse->getData(true);

        if (!isset($attractions[$attraction])) {
            return response()->json([
                'success' => false,
                'message' => 'Attraction not found.'
            ], 404);
        }

        // Validate booking details
        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'required',
            'guests' => 'required|integer|min:1',
            'special_requests' => 'nullable|string',
        ]);

        // Calculate total price based on number of guests
        $pricePerPerson = $attractions[$attraction]['price'];
        $numberOfGuests = (int)$validated['guests'];
        $subtotal = $pricePerPerson * $numberOfGuests;
        $tax = $subtotal * 0.14; // 14% tax
        $totalPrice = $subtotal + $tax;

        return response()->json([
            'success' => true,
            'data' => [
                'attraction' => $attractions[$attraction],
                'bookingDetails' => $validated,
                'pricing' => [
                    'pricePerPerson' => $pricePerPerson,
                    'subtotal' => $subtotal,
                    'tax' => $tax,
                    'total' => $totalPrice
                ]
            ]
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'trace' => $e->getTrace()
        ], 500);
    }
}


public function processBookingApi(Request $request, $attraction):JsonResponse
{
    try {
        // Validate payment details
        $validated = $request->validate([
            'payment_method' => 'required|string',
            'date' => 'required|date',
            'time' => 'required',
            'guests' => 'required|integer|min:1',
            // Add more validation rules for payment details based on the selected payment method
            'card_number' => 'required_if:payment_method,credit_card',
            'expiry_date' => 'required_if:payment_method,credit_card',
            'cvv' => 'required_if:payment_method,credit_card',
            'cardholder_name' => 'required_if:payment_method,credit_card',
            'country' => 'required',
            'address' => 'required',
            'city' => 'required',
            'postal_code' => 'required',
            'terms' => 'required',
        ]);
        
        // Simulate successful payment (In a real app, integrate with a payment gateway)
        $paymentStatus = 'success'; // Simulated payment status
        
        // If payment is successful
        if ($paymentStatus === 'success') {
            return response()->json([
                'success' => true,
                'message' => 'Your booking has been confirmed! Payment was successful. Please check your email for confirmation details.',
                'data' => [
                    'attraction' => $this->getAttractions()[$attraction], // Assuming you have a method to get attraction details
                    'booking_details' => $validated,
                ]
            ]);
        }
        
        // In case of payment failure
        return response()->json([
            'success' => false,
            'message' => 'Payment failed. Please try again.',
        ], 400);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => $e->getMessage(),
            'trace' => $e->getTrace()
        ], 500);
    }
}
public function reviews($slug)
{
    // Find attraction by slug
    $attraction = Attraction::with(['reviews.tourist'])->get()->first(function ($item) use ($slug) {
        return Str::slug($item->AttractionName) === $slug;
    });

    if (!$attraction) {
        return response()->json([
            'message' => 'Attraction not found.'
        ], 404);
    }

    // Format reviews
    $reviews = $attraction->reviews->map(function ($review) {
        return [
            'id' => $review->id,
            'rating' => $review->rating,
            'comment' => $review->comment,
            'tourist' => $review->tourist ? [
                'id' => $review->tourist->id,
                'name' => $review->tourist->name,
                'email' => $review->tourist->email,
            ] : null,
            'created_at' => $review->created_at->toDateTimeString()
        ];
    });

    return response()->json([
        'attraction' => [
            'id' => $attraction->id,
            'name' => $attraction->AttractionName,
            'slug' => Str::slug($attraction->AttractionName),
            'averageRating' => $attraction->reviews->avg('rating'),
            'reviewCount' => $attraction->reviews->count(),
        ],
        'reviews' => $reviews,
        'categories' => $this->getCategories()->getData(true)['data']
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
    $attraction = Attraction::with('reviews')->get()->first(function ($attraction) use ($slug) {
        return Str::slug($attraction->AttractionName) === $slug;
    });

    if (!$attraction) {
        return response()->json([
            'message' => 'Attraction not found.'
        ], 404);
    }

    // Create a new review
    $review = new Review();
    $review->rating = $validated['rating'];
    $review->comment = $validated['comment'];
    $review->tourist_id = auth()->id(); // Assuming user is authenticated
    $review->attraction_id = $attraction->id;
    $review->save();

    // Load the tourist relation (so tourist data is available immediately)
    $review->load('tourist');

    // Format the review
    $formattedReview = [
        'id' => $review->id,
        'rating' => $review->rating,
        'comment' => $review->comment,
        'tourist' => $review->tourist ? [
            'id' => $review->tourist->id,
            'name' => $review->tourist->name,
            'email' => $review->tourist->email,
        ] : null,
        'created_at' => $review->created_at->toDateTimeString()
    ];

    // Return JSON response
    return response()->json([
        'message' => 'Your review has been submitted successfully!',
        'review' => $formattedReview
    ], 201);
}
}