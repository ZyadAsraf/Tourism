<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attraction;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;
use Illuminate\Support\Date;

use Stripe\Stripe;
use Stripe\StripeClient;
use Stripe\PaymentIntent;

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
            // In a real app, you would fetch this from the database
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
            // If item already exists, update quantity
            $cart[$slug]['quantity'] += $validated['quantity'];
        } else {
            // Add new item to cart
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
        // Validate request
        $validated = $request->validate([
            'quantity' => 'required|integer|min:1',
        ]);
        
        // Get current cart
        $cart = Session::get('cart', []);
        
        // Update item quantity if it exists
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
        // Get current cart
        $cart = Session::get('cart', []);
        
        // Remove item if it exists
        if (isset($cart[$slug])) {
            unset($cart[$slug]);
            
            // Save cart back to session
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
        
        // Get attraction details for checkout
        $attractions = [];
        $total = 0;
        
        // Create an instance of AttractionController
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
        
        return view('cart.checkout', [
            'attractions' => $attractions,
            'total' => $total,
            'categories' => $attractionController->getCategories()
        ]);
    }
    
    /**
     * Process the checkout
     */
    public function processCheckout(Request $request)
    {
        // Validate checkout form
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'payment_method' => 'required|string',
            'terms' => 'required',
        ]);
        
        // In a real app, you would:
        // 1. Save the booking to the database
        // 2. Process payment
        // 3. Send confirmation email
        
        // Clear the cart after successful checkout
        Session::forget('cart');
        
        return redirect()->route('cart.confirmation')->with('success', 'Your trip has been booked successfully!');
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
     * Ensure all cart items have a time field
     */
    private function ensureCartHasTime($cart)
    {
        foreach ($cart as $slug => &$item) {
            if (!isset($item['time'])) {
                $item['time'] = '00:00:00';
            }
        }
        return $cart;
    }

    public function store(Request $request)
    {
        try {
            // Set your Stripe API key
            \Stripe\Stripe::setApiKey(config('services.stripe.secret'));

            // Create a charge with the token
            $charge = \Stripe\Charge::create([
                'amount' => $request->total * 100, // Amount in cents
                'currency' => 'usd',
                'source' => $request->stripeToken,
                'description' => 'Tourism App Payment',
            ]);

            // If we get here, the payment was successful
            // Create tickets for each attraction in the cart
            $cartItems = Session::get('cart', []);
            $attractionController = new AttractionController();
            $allAttractions = $attractionController->getAttractions();

            foreach ($cartItems as $slug => $item) {
                if (isset($allAttractions[$slug])) {
                    $attraction = $allAttractions[$slug];
                    
                    // Create ticket for each quantity
                    for ($i = 0; $i < $item['quantity']; $i++) {
                        \App\Models\Ticket::create([
                            'AttractionId' => $attraction['id'],
                            'CustomerId' => Auth::id() ?? 1,
                            'VisitDate' => $item['date'] . ' ' . ($item['time'] ?? '00:00:00'),
                            'BookingTime' => now(),
                            'QRCode' => Str::random(32),
                            'AttractionStaffId' => Auth::id() ?? 1,
                            'PaymentStatus' => 'paid',
                            'PaymentMethod' => 'credit_card',
                            'PaymentAmount' => $attraction['price'],
                            'PaymentDate' => now(),
                            'PaymentReference' => $charge->id,
                        ]);
                    }
                }
            }

            // Clear the cart
            Session::forget('cart');

            return redirect()->route('cart.confirmation')
                ->with('success', 'Payment successful! Your tickets have been created.');

        } catch (\Stripe\Exception\CardException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Stripe\Exception\InvalidRequestException $e) {
            return back()->with('error', 'Invalid payment details. Please check your card information and try again.');
        } catch (\Exception $e) {
            return back()->with('error', 'Payment failed: ' . $e->getMessage());
        }
    }
}
