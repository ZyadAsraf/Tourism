<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attraction;
use Illuminate\Support\Facades\Session;
use App\Models\Ticket;
use App\Models\TicketType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class CartController extends Controller
{
    /**
     * Display the cart/trip planning page
     */
    public function index()
    {
        $cartItems = Session::get('cart', []);
        $attractions = [];
        $total = 0;

        // Create an instance of AttractionController
        $attractionController = new AttractionController();
        $allAttractions = $attractionController->getAttractions();

        // Get attraction details for each item in cart
        foreach ($cartItems as $slug => $item) {
            if (isset($allAttractions[$slug])) {
                $attraction = $allAttractions[$slug];
                $attraction['quantity'] = $item['quantity'];
                $attraction['date'] = $item['date'];
                $attraction['time'] = $item['time'];
                $attraction['subtotal'] = $attraction['price'] * $item['quantity'];

                $attractions[] = $attraction;
                $total += $attraction['subtotal'];
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
        // Validate request
        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'required',
            'quantity' => 'required|integer|min:1',
        ]);

        // Get current cart
        $cart = Session::get('cart', []);

        // Add or update item in cart
        if (isset($cart[$slug])) {
            $cart[$slug]['quantity'] += $validated['quantity'];
        } else {
            $cart[$slug] = [
                'quantity' => $validated['quantity'],
                'date' => $validated['date'],
                'time' => $validated['time'],
            ];
        }

        // Save cart back to session
        Session::put('cart', $cart);

        return redirect()->route('cart.index')->with('success', 'Attraction added to your trip plan!');
    }

    /**
     * Update cart item quantity
     */
    public function update(Request $request, $slug)
    {
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);

        // Get current cart
        $cart = Session::get('cart', []);

        if (isset($cart[$slug])) {
            $cart[$slug]['quantity'] = $validated['quantity'];

            // Save cart back to session
            Session::put('cart', $cart);

            return redirect()->route('cart.index')->with('success', 'Trip plan updated!');
        }

        return redirect()->route('cart.index')->with('error', 'Attraction not found in your trip plan.');
    }

    /**
     * Remove an attraction from the cart
     */
    public function remove($slug)
    {
        $cart = Session::get('cart', []);

        if (isset($cart[$slug])) {
            unset($cart[$slug]);

            Session::put('cart', $cart);

            return redirect()->route('cart.index')->with('success', 'Attraction removed from your trip plan.');
        }

        return redirect()->route('cart.index')->with('error', 'Attraction not found in your trip plan.');
    }

    /**
     * Clear the entire cart
     */
    public function clear()
    {
        Session::forget('cart');

        return redirect()->route('cart.index')->with('success', 'Your trip plan has been cleared.');
    }

    /**
     * Proceed to checkout
     */
    public function checkout()
    {
        $cartItems = Session::get('cart', []);
    
        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('error', 'Your trip plan is empty. Add some attractions before checkout.');
        }
    
        $attractions = [];
        $total = 0;
    
        $attractionController = new AttractionController();
        $allAttractions = $attractionController->getAttractions();
    
        foreach ($cartItems as $slug => $item) {
            if (isset($allAttractions[$slug])) {
                $attraction = $allAttractions[$slug];
                $attraction['quantity'] = $item['quantity'];
                $attraction['date'] = $item['date'];
                $attraction['time'] = $item['time'];
                $attraction['subtotal'] = $attraction['price'] * $item['quantity'];
    
                $attractions[] = $attraction;
                $total += $attraction['subtotal'];
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
        $validated = $request->validate([
            'PhoneNumber' => 'required|string',
            'BookingTime' => 'required|date',
            'Quantity' => 'required|integer|min:1',
            'VisitDate' => 'required|date', 
            'TotalCost' => 'required|numeric',
            'state' => 'required|string',
            // 'AttractionStaffId' => 'required|exists:users,id',  
            'TicketTypesId' => 'required|exists:ticket_types,id', 
        ]);

        $validated['TouristId'] = Auth::id(); // Set from current logged-in user
        $validated['BookingTime'] = date('Y-m-d H:i:s', strtotime($validated['BookingTime']));
        $ticket = Ticket::create($validated);
        
        // Loop through cart items and attach attractions to the ticket
        $cartItems = Session::get('cart', []);
        foreach ($cartItems as $slug => $item) {
            try {
                $attraction = Attraction::where('slug', $slug)->firstOrFail();
                $ticket->attractions()->attach($attraction->id, [
                    'quantity' => $item['quantity'],
                    'visit_date' => $item['date'],
                ]);
            } catch (\Exception $e) {
                Log::error("Failed to attach attraction with slug {$slug}: " . $e->getMessage());
                continue;
            }
        }

        // Clear the cart after successful checkout
        Session::forget('cart');

        return redirect()->route('cart.confirmation')->with('success', 'Your booking is complete!');
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
        $cart = Session::get('cart', []);
        return count($cart);
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
