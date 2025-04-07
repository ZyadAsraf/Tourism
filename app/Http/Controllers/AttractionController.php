<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attraction;
use App\Models\Category;
use Illuminate\Support\Str;

class AttractionController extends Controller
{
  // Get attractions from the database
  private function getAttractions()
  {
    // Fetch attractions from the database that are marked as Available
    $attractions = Attraction::where('Status', 'Available')
        ->with(['governorate', 'categories', 'admin'])
        ->get();
    
    $formattedAttractions = [];
    
    foreach ($attractions as $attraction) {
        $slug = Str::slug($attraction->AttractionName);
        
        $formattedAttractions[$slug] = [
            'id' => $attraction->id,
            'title' => $attraction->AttractionName,
            'slug' => $slug,
            'price' => $attraction->EntryFee,
            'rating' => rand(4, 5) . '.' . rand(0, 9), // Generate random rating for now
            'reviewCount' => rand(1000, 10000), // Generate random review count for now
            'description' => strip_tags(Str::markdown($attraction->Description)),
            'longDescription' => strip_tags(Str::markdown($attraction->Description)),
            'image' => $attraction->Img ? '/storage/' . $attraction->Img : '/images/placeholder.jpg',
            'gallery' => $attraction->Img ? ['/storage/' . $attraction->Img] : ['/images/placeholder.jpg'],
            'mapImage' => $attraction->LocationLink ?? '/images/map-placeholder.jpg',
            'category' => $attraction->categories->isNotEmpty() ? $attraction->categories->first()->Name : 'Attraction',
            'location' => $attraction->City ?? $attraction->governorate->Name ?? 'Egypt',
            'duration' => '4 hours', // Default duration
            'included' => ['Entrance fees', 'Guide'],
            'notIncluded' => ['Transportation', 'Meals', 'Gratuities'],
            'featured' => true
        ];
    }
    
    // If no attractions found, return at least one placeholder
    if (empty($formattedAttractions)) {
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
            'duration' => '4 hours',
            'included' => ['Entrance fees', 'Guide'],
            'notIncluded' => ['Transportation', 'Meals', 'Gratuities'],
            'featured' => true
        ];
    }
    
    return $formattedAttractions;
}

  private function getCategories()
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

  public function index()
  {
      return view('attraction.index', [
          'attractions' => $this->getAttractions(),
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
      
      return view('attraction.show', [
          'attraction' => $attractions[$slug],
          'related' => array_slice($related, 0, 3),
          'categories' => $this->getCategories()
      ]);
  }

  public function byCategory($category)
  {
      $attractions = $this->getAttractions();
      $filtered = array_filter($attractions, function($attraction) use ($category) {
          return $attraction['category'] === $category;
      });
      
      return view('attraction.category', [
          'attractions' => $filtered,
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

  public function bookingForm($attraction)
  {
      $attractions = $this->getAttractions();
      
      if (!isset($attractions[$attraction])) {
          abort(404);
      }
      
      return view('booking.form', [
          'attraction' => $attractions[$attraction],
          'categories' => $this->getCategories()
      ]);
  }
  
  public function paymentForm(Request $request, $attraction)
  {
      $attractions = $this->getAttractions();
      
      if (!isset($attractions[$attraction])) {
          abort(404);
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
      
      return view('booking.payment', [
          'attraction' => $attractions[$attraction],
          'bookingDetails' => $validated,
          'categories' => $this->getCategories(),
          'pricing' => [
              'pricePerPerson' => $pricePerPerson,
              'subtotal' => $subtotal,
              'tax' => $tax,
              'total' => $totalPrice
          ]
      ]);
  }

  public function processBooking(Request $request, $attraction)
  {
      // In a real app, this would validate and save the booking and payment details to a database
      
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
      
      // Process payment (in a real app, this would integrate with a payment gateway)
      // For now, just simulate a successful payment
      
      // Redirect to confirmation page with success message
      return redirect()->route('attractions.show', $attraction)->with('success', 'Your booking has been confirmed! Payment was successful. Please check your email for confirmation details.');
  }
}

