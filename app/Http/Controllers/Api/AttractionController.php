<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

use App\Models\Attraction;
use App\Models\Category;
use Illuminate\Support\Str;
use URL;

class AttractionController extends Controller
{
// Get attractions from the database
public function getAttractions(Request $request = null)
{
    $query = Attraction::where('Status', 'Available')
        ->with(['governorate', 'categories', 'admin']);

    // Apply filters
    if ($request) {
        if ($request->has('categories') && !empty($request->categories)) {
            $categoryNames = [];
            $categories = Category::all();

            foreach ($request->categories as $slug) {
                foreach ($categories as $category) {
                    if (Str::slug($category->Name) === $slug) {
                        $categoryNames[] = $category->Name;
                    }
                }
            }

            if (!empty($categoryNames)) {
                $query->whereHas('categories', function ($q) use ($categoryNames) {
                    $q->whereIn('Name', $categoryNames);
                });
            }
        }

        if ($request->has('min_price') && is_numeric($request->min_price)) {
            $query->where('EntryFee', '>=', $request->min_price);
        }

        if ($request->has('max_price') && is_numeric($request->max_price)) {
            $query->where('EntryFee', '<=', $request->max_price);
        }
    }

    $attractions = $query->get();
    $formattedAttractions = [];

    $durationMap = [
        1 => 'short',
        2 => 'medium',
        3 => 'full_day',
        4 => 'multi_day',
    ];

    $durationText = [
        'short' => 'Less than 3 hours',
        'medium' => '3-5 hours',
        'full_day' => 'Full day',
        'multi_day' => 'Multi-day',
    ];

    foreach ($attractions as $attraction) {
        // Handle title
        $rawTitle = $attraction->AttractionName;
        $title = ['en' => '', 'ar' => ''];

        if (is_string($rawTitle)) {
            $decoded = json_decode($rawTitle, true);
            if (is_array($decoded)) {
                $title = $decoded;
            } else {
                $title['en'] = $rawTitle;
            }
        } elseif (is_array($rawTitle)) {
            $title = $rawTitle;
        }

        // Handle description
        $rawDesc = $attraction->Description;
        $description = ['en' => '', 'ar' => ''];

        if (is_string($rawDesc)) {
            $decoded = json_decode($rawDesc, true);
            if (is_array($decoded)) {
                $description = $decoded;
            } else {
                $description['en'] = $rawDesc;
            }
        } elseif (is_array($rawDesc)) {
            $description = $rawDesc;
        }

        $slug = Str::slug($title['en'] ?? 'attraction');

        $durationType = $durationMap[$attraction->id % 4 + 1] ?? 'medium';
        $duration = $durationText[$durationType];

        if ($request && $request->has('durations') && !empty($request->durations)) {
            if (!in_array($durationType, $request->durations)) {
                continue;
            }
        }

        $formattedAttractions[$slug] = [
            'id' => $attraction->id,
            'title_en' => $title['en'] ?? '',
            'title_ar' => $title['ar'] ?? '',
            'slug' => $slug,
            'price' => $attraction->EntryFee,
            'rating' => rand(4, 5) . '.' . rand(0, 9),
            'reviewCount' => rand(1000, 10000),
            'description_en' => $description['en'] ?? '',
            'description_ar' => $description['ar'] ?? '',
            'image' => $attraction->Img ? '/storage/' . $attraction->Img : '/images/placeholder.jpg',
            'gallery' => $attraction->Img ? ['/storage/' . $attraction->Img] : ['/images/placeholder.jpg'],
            'mapImage' => $attraction->LocationLink ?? '/images/map-placeholder.jpg',
            'category' => $attraction->categories->isNotEmpty() ? $attraction->categories->first()->Name : 'Attraction',
            'location' => $attraction->City ?? $attraction->governorate->Name ?? 'Egypt',
            'duration' => $duration,
            'durationType' => $durationType,
            'included' => ['Entrance fees', 'Guide'],
            'notIncluded' => ['Transportation', 'Meals', 'Gratuities'],
            'featured' => true
        ];
    }

    if (empty($formattedAttractions) && (!$request || (!$request->has('categories') && !$request->has('durations') && !$request->has('min_price') && !$request->has('max_price')))) {
        $formattedAttractions['sample-attraction'] = [
            'id' => 1,
            'title_en' => 'Sample Attraction',
            'title_ar' => 'معلم تجريبي',
            'title' => app()->getLocale() === 'ar' ? 'معلم تجريبي' : 'Sample Attraction',
            'slug' => 'sample-attraction',
            'price' => 100,
            'rating' => 4.5,
            'reviewCount' => 1000,
            'description_en' => 'This is a sample attraction.',
            'description_ar' => 'هذا معلم سياحي تجريبي.',
            'description' => app()->getLocale() === 'ar' ? 'هذا معلم سياحي تجريبي.' : 'This is a sample attraction.',
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
    $title = is_array($attraction['title']) ? ($attraction['title']['en'] ?? reset($attraction['title'])) : $attraction['title'];
    $description = is_array($attraction['description']) ? ($attraction['description']['en'] ?? reset($attraction['description'])) : $attraction['description'];
    $location = $attraction['location'] ?? '';

    return stripos($title, $query) !== false ||
           stripos($description, $query) !== false ||
           stripos($location, $query) !== false;
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
                    'attraction' => $this->getAttractions()->getData(true)[$attraction], // Assuming you have a method to get attraction details
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

}
