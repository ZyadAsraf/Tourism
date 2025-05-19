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
        
        foreach ($cartItems as $item) {
            $attraction = Attraction::find($item->attraction_id);
            if ($attraction && isset($allAttractions[$attraction->slug])) {
                $attractionData = $allAttractions[$attraction->slug];
                $attractionData['quantity'] = $item->quantity;
                $attractionData['date'] = $item->date;
                $attractionData['time'] = $item->time;
                $attractionData['ticket_type_id'] = $item->ticket_type_id;
                
                // Calculate subtotal from attraction price
                $subtotal = $attraction->price * $item->quantity;
                $attractionData['subtotal'] = $subtotal;
                
                $attractions[] = $attractionData;
                $total += $subtotal;
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
        
        // Find the attraction
        $attraction = Attraction::where('slug', $slug)->first();
        
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
     * Add multiple attractions to the cart at once
     */
    public function massAdd(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.attraction_id' => 'required|exists:attractions,id',
            'items.*.date' => 'required|date',
            'items.*.time' => 'required',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.ticket_type_id' => 'required|exists:ticket_types,id',
        ]);
        
        $addedItems = 0;
        
        foreach ($validated['items'] as $item) {
            // Find the attraction
            $attraction = Attraction::find($item['attraction_id']);
            
            if (!$attraction) {
                continue;
            }
            
            if (!Auth::check()) {
                // For guests, use session storage
                $cart = Session::get('cart', []);
                $slug = $attraction->slug;
                
                // Add or update item in cart
                if (isset($cart[$slug])) {
                    $cart[$slug]['quantity'] += $item['quantity'];
                } else {
                    $cart[$slug] = [
                        'quantity' => $item['quantity'],
                        'date' => $item['date'],
                        'time' => $item['time'],
                        'uuid' => Str::uuid(),
                        'ticket_type_id' => $item['ticket_type_id'],
                    ];
                }
                
                // Save cart back to session
                Session::put('cart', $cart);
            } else {
                // For authenticated users, store in database
                $price = $attraction->price;
                
                // Check if the attraction is already in the user's cart
                $cartItem = CartItem::where('user_id', Auth::id())
                    ->where('attraction_id', $attraction->id)
                    ->where('date', $item['date'])
                    ->where('time', $item['time'])
                    ->first();
                    
                if ($cartItem) {
                    // Update existing cart item
                    $cartItem->quantity += $item['quantity'];
                    $cartItem->subtotal = $price * $cartItem->quantity;
                    $cartItem->save();
                } else {
                    // Create new cart item
                    CartItem::create([
                        'user_id' => Auth::id(),
                        'attraction_id' => $attraction->id,
                        'ticket_type_id' => $item['ticket_type_id'],
                        'quantity' => $item['quantity'],
                        'price' => $price,
                        'subtotal' => $price * $item['quantity'],
                        'date' => $item['date'],
                        'time' => $item['time'],
                        'uuid' => Str::uuid(),
                    ]);
                }
            }
            
            $addedItems++;
        }
        
        return response()->json([
            'success' => true,
            'message' => $addedItems . ' attractions added to your Cart!',
            'items_added' => $addedItems
        ]);
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $slug = null, $uuid = null)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        if (Auth::check()) {
            // For authenticated users, update from database
            // If UUID is provided, use it. Otherwise, find by attraction slug
            $query = CartItem::where('user_id', Auth::id());
            
            if ($uuid) {
                $query->where('uuid', $uuid);
            } else {
                $attraction = Attraction::where('slug', $slug)->first();
                if (!$attraction) {
                    return redirect()->route('cart.index')->with('error', 'Attraction not found.');
                }
                $query->where('attraction_id', $attraction->id);
            }
            
            $cartItem = $query->first();
            
            if ($cartItem) {
                $cartItem->quantity = $validated['quantity'];
                $cartItem->subtotal = $cartItem->price * $validated['quantity'];
                $cartItem->save();
                
                return redirect()->route('cart.index')->with('success', 'Cart updated!');
            }
        } else {
            // For guests, use session storage
            $cart = Session::get('cart', []);

            if (isset($cart[$slug])) {
                $cart[$slug]['quantity'] = $validated['quantity'];

                // Save cart back to session
                Session::put('cart', $cart);

                return redirect()->route('cart.index')->with('success', 'Cart updated!');
            }
        }

        return redirect()->route('cart.index')->with('error', 'Attraction not found in your Cart.');
    }

    /**
     * Remove an attraction from the cart
     */
    public function remove($identifierParam)
    {
        if (Auth::check()) {
            // For authenticated users, remove from database
            // First check if $identifierParam is a UUID
            $cartItem = CartItem::where('user_id', Auth::id())
                               ->where('uuid', $identifierParam)
                               ->first();
            
            if (!$cartItem) {
                // Try to find by attraction slug
                $attraction = Attraction::where('slug', $identifierParam)->first();
                
                if ($attraction) {
                    $cartItem = CartItem::where('user_id', Auth::id())
                                       ->where('attraction_id', $attraction->id)
                                       ->first();
                }
            }
            
            if ($cartItem) {
                $cartItem->delete();
                return redirect()->route('cart.index')->with('success', 'Attraction removed from your Cart.');
            }
        } else {
            // For guests, use session
            $cart = Session::get('cart', []);

            // Try the parameter as a slug first
            if (isset($cart[$identifierParam])) {
                unset($cart[$identifierParam]);
                Session::put('cart', $cart);
                return redirect()->route('cart.index')->with('success', 'Attraction removed from your Cart.');
            }
            
            // If not found as a slug, check if it's a UUID
            foreach ($cart as $slug => $item) {
                if (isset($item['uuid']) && $item['uuid'] === $identifierParam) {
                    unset($cart[$slug]);
                    Session::put('cart', $cart);
                    return redirect()->route('cart.index')->with('success', 'Attraction removed from your Cart.');
                }
            }
        }

        return redirect()->route('cart.index')->with('error', 'Attraction not found in your Cart.');
    }

    /**
     * Clear the entire cart
     */
    public function clear()
    {
        if (Auth::check()) {
            // For authenticated users, clear from database
            CartItem::where('user_id', Auth::id())->delete();
        } else {
            // For guests, clear from session
            Session::forget('cart');
        }

        return redirect()->route('cart.index')->with('success', 'Your Cart has been cleared.');
    }

    /**
     * Proceed to checkout
     */
    public function checkout()
    {
        $attractions = [];
        $total = 0;
        $attractionController = new AttractionController();
        $allAttractions = $attractionController->getAttractions();
        
        if (Auth::check()) {
            // For authenticated users, get from database
            $dbCartItems = CartItem::where('user_id', Auth::id())->get();
            
            if ($dbCartItems->isEmpty()) {
                return redirect()->route('cart.index')->with('error', 'Your Cart is empty. Add some attractions before checkout.');
            }
            
            foreach ($dbCartItems as $item) {
                $attraction = Attraction::find($item->attraction_id);
                if ($attraction && isset($allAttractions[$attraction->slug])) {
                    $attractionData = $allAttractions[$attraction->slug];
                    $attractionData['quantity'] = $item->quantity;
                    $attractionData['date'] = $item->date;
                    $attractionData['time'] = $item->time;
                    $attractionData['subtotal'] = $item->subtotal;
                    $attractionData['uuid'] = $item->uuid;
                    $attractionData['ticket_type_id'] = $item->ticket_type_id;
                    
                    $attractions[] = $attractionData;
                    $total += $item->subtotal;
                }
            }
        } else {
            // For guests, get from session
            $sessionCartItems = Session::get('cart', []);
            
            if (empty($sessionCartItems)) {
                return redirect()->route('cart.index')->with('error', 'Your Cart is empty. Add some attractions before checkout.');
            }
            
            foreach ($sessionCartItems as $slug => $item) {
                if (isset($allAttractions[$slug])) {
                    $attraction = $allAttractions[$slug];
                    $attraction['quantity'] = $item['quantity'];
                    $attraction['date'] = $item['date'];
                    $attraction['time'] = $item['time'];
                    $attraction['subtotal'] = $attraction['price'] * $item['quantity'];
                    $attraction['uuid'] = $item['uuid'] ?? Str::uuid();
                    $attraction['ticket_type_id'] = $item['ticket_type_id'] ?? 1;
                    
                    $attractions[] = $attraction;
                    $total += $attraction['subtotal'];
                }
            }
        }
    
        $ticketTypes = TicketType::all();
    
        return view('cart.checkout', [
            'attractions' => $attractions, // Passed as an array
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
        // Validate common fields
        $validated = $request->validate([
            'PhoneNumber' => 'required|string',
            'state' => 'required|string',
            'TicketTypesId' => 'required|exists:ticket_types,id', 
        ]);

        $ticketsCreated = 0;
        
        // Fetch all attractions to verify they exist
        $attractionController = new AttractionController();
        $allAttractions = $attractionController->getAttractions();
        
        if (Auth::check()) {
            // For authenticated users, get items from database
            $cartItems = CartItem::where('user_id', Auth::id())->get();
            
            // Create a ticket for each attraction in the cart
            foreach ($cartItems as $item) {
                try {
                    $attraction = Attraction::find($item->attraction_id);
                    
                    if (!$attraction || !isset($allAttractions[$attraction->slug])) {
                        Log::error("Attraction with ID {$item->attraction_id} not found in attractions list");
                        continue;
                    }
                    
                    // Create a new ticket for this attraction
                    $ticketData = [
                        'TouristId' => Auth::id(),
                        'PhoneNumber' => $validated['PhoneNumber'],
                        'BookingTime' => now(),
                        'Quantity' => $item->quantity,
                        'VisitDate' => $item->date,
                        'TimeSlot' => $item->time,
                        'TotalCost' => $item->subtotal,
                        'state' => $validated['state'],
                        'Attraction' => $attraction->slug,
                        'TicketTypesId' => $item->ticket_type_id ?? $validated['TicketTypesId'],
                    ];
                    
                    Ticket::create($ticketData);
                    $ticketsCreated++;
                } catch (\Exception $e) {
                    Log::error("Failed to create ticket for attraction ID {$item->attraction_id}: " . $e->getMessage());
                    return redirect()->route('cart.index')
                        ->with('error', 'Failed to create ticket: ' . $e->getMessage());
                }
            }
            
            // Clear the cart after successful checkout if tickets were created
            if ($ticketsCreated > 0) {
                CartItem::where('user_id', Auth::id())->delete();
            }
        } else {
            // For guests, use session storage
            $sessionCartItems = Session::get('cart', []);
            
            // Create a ticket for each attraction in the cart
            foreach ($sessionCartItems as $slug => $item) {
                try {
                    // Check if the attraction exists in our list
                    if (!isset($allAttractions[$slug])) {
                        Log::error("Attraction with slug {$slug} not found in attractions list");
                        continue;
                    }
                
                    // Create a new ticket for this attraction
                    $ticketData = [
                        'TouristId' => Auth::id() ?? 0, // Guest user
                        'PhoneNumber' => $validated['PhoneNumber'],
                        'BookingTime' => now(),
                        'Quantity' => $item['quantity'],
                        'VisitDate' => $item['date'],
                        'TimeSlot' => $item['time'],
                        'TotalCost' => $allAttractions[$slug]['price'] * $item['quantity'],
                        'state' => $validated['state'],
                        'Attraction' => $slug,
                        'TicketTypesId' => $item['ticket_type_id'] ?? $validated['TicketTypesId'],
                    ];
                    
                    Ticket::create($ticketData);
                    $ticketsCreated++;
                } catch (\Exception $e) {
                    Log::error("Failed to create ticket for attraction with slug {$slug}: " . $e->getMessage());
                    return redirect()->route('cart.index')
                        ->with('error', 'Failed to create ticket for attraction: ' . $e->getMessage());
                }
            }
            
            // Clear the session cart after successful checkout if tickets were created
            if ($ticketsCreated > 0) {
                Session::forget('cart');
            }
        }

        // Redirect based on the result
        if ($ticketsCreated > 0) {
            // Clear the cart after successful checkout
            Session::forget('cart');
            return redirect()->route('cart.confirmation')
                ->with('success', 'Your bookings are complete! Created ' . $ticketsCreated . ' tickets.');
            } else {
            return redirect()->route('cart.index')
                ->with('error', 'Failed to create tickets. Cart data: ' . json_encode($cartItems));
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
            return CartItem::where('user_id', Auth::id())->sum('quantity');
        } else {
            // For guests, count from session
            $cart = Session::get('cart', []);
            $count = 0;
            foreach ($cart as $item) {
                $count += $item['quantity'] ?? 1;
            }
            return $count;
        }
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
