<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attraction;
use App\Models\CartItem;
use Illuminate\Support\Facades\Session;
use App\Models\Ticket;
use App\Models\TicketType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Stripe\StripeClient;

class CartController extends Controller
{
    /**
     * Display the cart planning page
     */
    public function index()
    {
        // Only authenticated users can have a cart
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view your cart.');
        }
        
        $attractions = [];
        $total = 0;
        
        // Create an instance of AttractionController
        $attractionController = new AttractionController();
        $allAttractions = $attractionController->getAttractions();
        
        // Get cart items from database
        $cartItems = CartItem::where('user_id', Auth::id())->get();
        
        // Get attraction IDs for batch review stats retrieval
        $attractionIds = $cartItems->pluck('attraction_id')->toArray();
        $reviewStats = $attractionController->getMultipleAttractionReviewStats($attractionIds);
        
        foreach ($cartItems as $item) {
            $attraction = Attraction::find($item->attraction_id);
            if ($attraction) {
                // Generate slug from AttractionName to match with allAttractions array
                $attractionSlug = Str::slug($attraction->AttractionName);
                $attractionData = null;
                
                // Check direct match by slug
                if (isset($allAttractions[$attractionSlug])) {
                    $attractionData = $allAttractions[$attractionSlug];
                } else {
                    // Search through attractions for a match by ID
                    foreach ($allAttractions as $slug => $data) {
                        if ($data['id'] == $attraction->id) {
                            $attractionData = $data;
                            break;
                        }
                    }
                }
                
                // If we found the attraction data, add it to the cart
                if ($attractionData) {
                    $attractionData['quantity'] = $item->quantity;
                    $attractionData['date'] = $item->date;
                    $attractionData['time'] = $item->time;
                    $attractionData['ticket_type_id'] = $item->ticket_type_id;
                    
                    // Add review statistics
                    if (isset($reviewStats[$attraction->id])) {
                        $attractionData['rating'] = $reviewStats[$attraction->id]['average_rating'];
                        $attractionData['reviewCount'] = $reviewStats[$attraction->id]['review_count'];
                    }
                    
                    // Calculate subtotal from attraction price
                    $subtotal = $attractionData['price'] * $item->quantity;
                    $attractionData['subtotal'] = $subtotal;
                    
                    $attractions[] = $attractionData;
                    $total += $subtotal;
                }
            }
        }

        return view('cart.index', [
            'attractions' => $attractions,
            'total' => $total,
            'categories' => $attractionController->getCategories()
        ]);
    }

    /**
     * Add an attraction to the cart
     */
    
public function add(Request $request, $slug)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to add attractions to your Cart.');
        }
        
        // Validate request
        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'ticket_type_id' => 'required|exists:ticket_types,id',
        ]);
        
        // Find the attraction by converting slug back to attraction name
        // Since slug is created by Str::slug($attraction->AttractionName), we need to find by matching
        $attraction = Attraction::all()->first(function ($attraction) use ($slug) {
            return Str::slug($attraction->AttractionName) === $slug;
        });
        
        if (!$attraction) {
            return redirect()->route('cart.index')->with('error', 'Attraction not found.');
        }
        
        // Check if the attraction is already in the user's cart
        $cartItem = CartItem::where('user_id', Auth::id())
            ->where('attraction_id', $attraction->id)
            ->where('date', $validated['date'])
            ->where('time', $validated['time'])
            ->where('ticket_type_id', $validated['ticket_type_id'])
            ->first();
        
        if ($cartItem) {
            // Update existing cart item
            $cartItem->quantity += $validated['quantity'];
            $cartItem->save();
        } else {
            // Create new cart item
            CartItem::create([
                'user_id' => Auth::id(),
                'attraction_id' => $attraction->id,
                'ticket_type_id' => $validated['ticket_type_id'],
                'quantity' => $validated['quantity'],
                'date' => $validated['date'],
                'time' => $validated['time'],
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Attraction added to your Cart!');
    }
    
    /**
     * Add all attractions from an itinerary to the cart
     */
    public function addItinerary(Request $request)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to add attractions to your Cart.');
        }

        // Validate request
        $validated = $request->validate([
            'itinerary_uuid' => 'required|exists:itineraries,uuid',
        ]);
        
        // Get itinerary
        $itinerary = \App\Models\Itinerary::where('uuid', $validated['itinerary_uuid'])->first();
        
        if (!$itinerary) {
            return redirect()->back()->with('error', 'Itinerary not found.');
        }
        
        // Get all itinerary items
        $itineraryItems = \App\Models\ItineraryItem::where('itinerary_id', $itinerary->uuid)->get();
        
        if ($itineraryItems->isEmpty()) {
            return redirect()->back()->with('error', 'This itinerary is empty. Add attractions before adding to cart.');
        }
        
        $addedItems = 0;
        $errors = [];
        // dd($itineraryItems);
        foreach ($itineraryItems as $item) {
            // Find the attraction
            $attraction = Attraction::find($item->attraction_id);
            
            if (!$attraction) {
                $errors[] = "One of the attractions could not be found.";
                continue;
            }
            
            // Check if the attraction is already in the user's cart
            $cartItem = CartItem::where('user_id', Auth::id())
                ->where('attraction_id', $attraction->id)
                ->where('date', $item->date)
                ->where('time', $item->time)
                ->where('ticket_type_id', $item->ticket_type_id)
                ->first();
            
            // Get attraction price
            $price = $attraction->price;
            
            if ($cartItem) {
                // Update existing cart item
                $cartItem->quantity += $item->quantity;
                $cartItem->save();
                $addedItems++;
            } else {
                // Create new cart item
                CartItem::create([
                    'user_id' => Auth::id(),
                    'attraction_id' => $attraction->id,
                    'ticket_type_id' => $item->TicketTypeId,
                    'quantity' => $item->quantity,
                    'date' => $item->date,
                    'time' => $item->time,
                ]);
                $addedItems++;
            }
        }
        
        if ($addedItems > 0) {
            return redirect()->route('cart.index')->with('success', "Added {$addedItems} attractions from your itinerary to your cart.");
        } else {
            return redirect()->back()->with('error', 'Failed to add items to cart: ' . implode(' ', $errors));
        }
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $slug = null, $uuid = null)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to update your Cart.');
        }

        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // For authenticated users, update from database
        // If UUID is provided, use it. Otherwise, find by attraction slug
        $query = CartItem::where('user_id', Auth::id());
        
        if ($uuid) {
            $query->where('uuid', $uuid);
        } elseif ($slug) {
            // Find attraction by converting slug back to attraction name
            $attraction = Attraction::all()->first(function ($attraction) use ($slug) {
                return Str::slug($attraction->AttractionName) === $slug;
            });
            
            if (!$attraction) {
                return redirect()->route('cart.index')->with('error', 'Attraction not found.');
            }
            $query->where('attraction_id', $attraction->id);
        } else {
            // Neither slug nor UUID provided
            return redirect()->route('cart.index')->with('error', 'Attraction identifier missing.');
        }
        
        $cartItem = $query->first();
        
        if ($cartItem) {
            // Get attraction data from AttractionController to get the correct price
            $attraction = Attraction::find($cartItem->attraction_id);
            if (!$attraction) {
                return redirect()->route('cart.index')->with('error', 'Could not find attraction for cart item.');
            }
            
            // Get price from AttractionController which has the correct price data
            $attractionController = new AttractionController();
            $allAttractions = $attractionController->getAttractions();
            
            // Try to get price from attractions data
            $price = null;
            if (isset($allAttractions[$slug])) {
                $price = $allAttractions[$slug]['price'];
            } else {
                // Fallback to the stored price or default
                $price = $cartItem->price ?? 0;
            }
            if (!$price) {
                return redirect()->route('cart.index')->with('error', 'Could not determine price for cart item.');
            }
            
            $cartItem->quantity = $validated['quantity'];
            $cartItem->save();
            
            return redirect()->route('cart.index')->with('success', 'Cart updated!');
        }

        return redirect()->route('cart.index')->with('error', 'Attraction not found in your Cart.');
    }

    /**
     * Remove an attraction from the cart
     */
    public function remove($identifierParam)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to modify your Cart.');
        }

        // For authenticated users, remove from database
        // First check if $identifierParam is a UUID (assuming UUIDs have specific format)
        $cartItem = null;
        
        // Try to find by UUID first
        if (preg_match('/^[0-9a-f]{8}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{4}-[0-9a-f]{12}$/i', $identifierParam)) {
            $cartItem = CartItem::where('user_id', Auth::id())
                            ->where('uuid', $identifierParam)
                            ->first();
        } else {
            // Try to find by attraction slug
            $attraction = Attraction::all()->first(function ($attraction) use ($identifierParam) {
                return Str::slug($attraction->AttractionName) === $identifierParam;
            });
            
            if ($attraction) {
                // Remove the first matching item by attraction_id
                $cartItem = CartItem::where('user_id', Auth::id())
                                ->where('attraction_id', $attraction->id)
                                ->first();
            }
        }
        
        if ($cartItem) {
            $cartItem->delete();
            return redirect()->route('cart.index')->with('success', 'Attraction removed from your Cart.');
        }

        return redirect()->route('cart.index')->with('error', 'Attraction not found in your Cart.');
    }

    /**
     * Clear the entire cart
     */
    public function clear()
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to clear your Cart.');
        }

        // For authenticated users, clear from database
        CartItem::where('user_id', Auth::id())->delete();

        return redirect()->route('cart.index')->with('success', 'Your Cart has been cleared.');
    }

    /**
     * Proceed to checkout
     */
    public function checkout()
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to proceed to checkout.');
        }

        $attractions = [];
        $total = 0;
        $attractionController = new AttractionController();
        $allAttractions = $attractionController->getAttractions();
        
        // For authenticated users, get from database
        $dbCartItems = CartItem::where('user_id', Auth::id())->get();
        
        if ($dbCartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your Cart is empty. Add some attractions before checkout.');
        }
        
        // Get attraction IDs for batch review stats retrieval
        $attractionIds = $dbCartItems->pluck('attraction_id')->toArray();
        $reviewStats = $attractionController->getMultipleAttractionReviewStats($attractionIds);
        
        foreach ($dbCartItems as $item) {
            $attractionModel = Attraction::find($item->attraction_id);
            if ($attractionModel) {
                // Generate slug from AttractionName to match with allAttractions array
                $attractionSlug = Str::slug($attractionModel->AttractionName);
                
                // Find attraction data by matching slug or ID
                $attractionData = null;
                if (isset($allAttractions[$attractionSlug])) {
                    $attractionData = $allAttractions[$attractionSlug];
                } else {
                    // Search through attractions for a match by ID
                    foreach ($allAttractions as $slug => $data) {
                        if ($data['id'] == $attractionModel->id) {
                            $attractionData = $data;
                            break;
                        }
                    }
                }
                
                if ($attractionData) {
                    $attractionData['quantity'] = $item->quantity;
                    $attractionData['date'] = $item->date;
                    $attractionData['time'] = $item->time;
                    $attractionData['ticket_type_id'] = $item->ticket_type_id;
                    $attractionData['cart_item_uuid'] = $item->uuid;

                    // Add review statistics
                    if (isset($reviewStats[$item->attraction_id])) {
                        $attractionData['rating'] = $reviewStats[$item->attraction_id]['average_rating'];
                        $attractionData['reviewCount'] = $reviewStats[$item->attraction_id]['review_count'];
                    }

                    // Use stored price or fallback to attraction's current price
                    $price = $item->price ?? $attractionModel->EntryFee;
                    $subtotal = $item->subtotal ?? ($price * $item->quantity);

                    $attractionData['price'] = $price;
                    $attractionData['subtotal'] = $subtotal;
                    
                    $attractions[] = $attractionData;
                    $total += $subtotal;
                }
            }
        }

        $ticketTypes = TicketType::all();

        return view('cart.checkout', [
            'attractions' => $attractions, 
            'total' => $total,
            'categories' => $attractionController->getCategories(),
            'ticketTypes' => $ticketTypes,
        ]);
    }
    
    /**
     * Process the checkout
     */
    public function processCheckout(Request $request)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to process checkout.');
        }

        // Validate common fields
        $validated = $request->validate([
            'PhoneNumber' => 'required|string',
            'state' => 'required|string',
        ]);

        $ticketsCreated = 0;
        
        // For authenticated users, get items from database
        $cartItems = CartItem::where('user_id', Auth::id())->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }
            
        // Create a ticket for each cart item
        foreach ($cartItems as $item) {
            $attraction = Attraction::find($item->attraction_id);
            if (!$attraction) {
                Log::error("Checkout: Attraction ID {$item->attraction_id} not found for User ID " . Auth::id());
                continue; // Skip this item
            }
            // Create Ticket
            // Get current price from attraction model
            $currentPrice = $attraction->EntryFee;
            Ticket::create([
                'TouristId' => Auth::id(),
                'Attraction' => $item->attraction_id,
                'TicketTypesId' => $item->ticket_type_id, 
                'Quantity' => $item->quantity,
                'BookingTime' => now(),
                'TotalCost' => $currentPrice * $item->quantity, // Using price directly from attraction
                'VisitDate' => $item->date,
                'TimeSlot' => $item->time,
                'PhoneNumber' => $validated['PhoneNumber'],
                'state' => $validated['state'],
            ]);
            $ticketsCreated++;
        }
            
        // Clear the cart after successful checkout if tickets were created
        if ($ticketsCreated > 0) {
            CartItem::where('user_id', Auth::id())->delete(); // Clear user's cart
            // Session::forget('cart'); // This was for guests, remove
            return redirect()->route('cart.confirmation')
                ->with('success', 'Your bookings are complete! Created ' . $ticketsCreated . ' tickets.');
        } else {
            // This case implies cart was not empty, but no tickets were created (e.g. all attractions not found)
            return redirect()->route('cart.index')
                         ->with('error', 'Could not process your booking. Please try again or contact support.');
        }
    }
    /**
     * Display checkout confirmation
     */
    public function confirmation()
    {
        $attractionController = new AttractionController();

        return view('cart.confirmation', [
            'categories' => $attractionController->getCategories()
        ]);
    }

    /**
     * Get cart count for display in header
     */
    public static function getCartCount()
    {
        if (Auth::check()) {
            // For authenticated users, count from database
            return CartItem::where('user_id', Auth::id())->count();
        }
        // For guests, count is 0 as they can't have a cart
        return 0;
    }

    /**
     * Handle Stripe Payment
     */
    public function store(Request $request)
    {
        $stripe = new StripeClient(env("STRIPE_SECRET"));

        $charge = $stripe->charges->create([
            'amount' => $request->total * 100,
            'currency' => 'usd',
            'source' => $request->stripeToken,
            'description' => 'Payment from Massar.com',
        ]);

        return redirect()->route('cart.confirmation');    }
}
