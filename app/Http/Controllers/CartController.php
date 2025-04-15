<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attraction;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

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
        $cart = Session::get('cart', []);
        $cart = $this->ensureCartHasTime($cart);
        Session::put('cart', $cart);
        
        $attractions = [];
        $total = 0;
        
        // Create an instance of AttractionController
        $attractionController = new AttractionController();
        $allAttractions = $attractionController->getAttractions();
        
        // Get attraction details for each item in cart
        foreach ($cart as $slug => $item) {
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
            'VisitDate' => 'required|date',
            'PhoneNumber' => 'required',
            'Quantity' => 'required|integer|min:1',
        ]);
        
        // Get current cart
        $cart = Session::get('cart', []);
        
        // Format the date properly
        try {
            // Parse the datetime input
            $visitDate = Date::createFromFormat('Y-m-d\TH:i', $validated['VisitDate']);
            if ($visitDate === false) {
                throw new \Exception('Invalid date format');
            }
            
            // Format to Y-m-d and H:i:s for storage
            $formattedDate = $visitDate->format('Y-m-d');
            $formattedTime = $visitDate->format('H:i:s');
            
        } catch (\Exception $e) {
            return redirect()->back()->with('error', 'Invalid date format provided');
        }
        
        // Add or update item in cart
        if (isset($cart[$slug])) {
            // If item already exists, update quantity
            $cart[$slug]['quantity'] += $validated['Quantity'];
        } else {
            // Add new item to cart
            $cart[$slug] = [
                'quantity' => $validated['Quantity'],
                'date' => $formattedDate,
                'time' => $formattedTime,
                'phone' => $validated['PhoneNumber'],
            ];
        }
        
        // Save cart back to session
        Session::put('cart', $cart);
        
        // Redirect to cart page with success message
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
        $cart = Session::get('cart', []);
        $cart = $this->ensureCartHasTime($cart);
        Session::put('cart', $cart);
        
        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Your trip plan is empty. Add some attractions before checkout.');
        }
        
        // Get attraction details for checkout
        $attractions = [];
        $total = 0;
        
        // Create an instance of AttractionController
        $attractionController = new AttractionController();
        $allAttractions = $attractionController->getAttractions();

        foreach ($cart as $slug => $item) {
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


    public function store(Request $request)
    {
        // Get cart items from session
        $cart = Session::get('cart', []);
        $cart = $this->ensureCartHasTime($cart);
        Session::put('cart', $cart);
        
        // Handle payment based on selected method
        if ($request->payment_method === 'credit_card') {
            // Validate Stripe token
            if (!$request->stripeToken) {
                return redirect()->back()->with('error', 'Payment token is missing');
            }

            // Initialize Stripe with proper configuration
            $stripe = new \Stripe\StripeClient([
                'api_key' => config('services.stripe.secret'),
                'stripe_version' => '2023-10-16'
            ]);

            try {
                $charge = $stripe->charges->create([
                    'amount' => $request->total * 100,
                    'currency' => 'usd',
                    'source' => $request->stripeToken,
                    'description' => 'Payment from Massar.com'
                ]);
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Payment failed: ' . $e->getMessage());
            }
        } elseif ($request->payment_method === 'paypal') {
            // In a real implementation, you would:
            // 1. Create a PayPal order
            // 2. Redirect to PayPal for payment
            // 3. Handle the PayPal callback
            // For now, we'll just proceed with ticket creation
        } elseif ($request->payment_method === 'pay_on_arrival') {
            // No payment processing needed for pay on arrival
        } else {
            return redirect()->back()->with('error', 'Invalid payment method selected');
        }

        // Create ticket records for each item in cart
        foreach ($cart as $slug => $item) {
            // Get attraction details
            $attractionController = new AttractionController();
            $allAttractions = $attractionController->getAttractions();
            
            if (isset($allAttractions[$slug])) {
                $attraction = $allAttractions[$slug];
                
                try {
                    // Create the datetime object directly
                    $visitDate = Date::create(
                        (int)substr($item['date'], 0, 4), // year
                        (int)substr($item['date'], 5, 2), // month
                        (int)substr($item['date'], 8, 2), // day
                        (int)substr($item['time'], 0, 2), // hour
                        (int)substr($item['time'], 3, 2), // minute
                        (int)substr($item['time'], 6, 2)  // second
                    );
                    
                    // Create ticket record
                    \App\Models\Ticket::create([
                        'PhoneNumber' => $request->phone,
                        'QRCode' => Str::random(32),
                        'BookingTime' => Date::now(),
                        'Quantity' => $item['quantity'],
                        'VisitDate' => $visitDate,
                        'TotalCost' => $attraction['price'] * $item['quantity'],
                        'TouristId' => Auth::id(),
                        'AttractionId' => $attraction['id'],
                        'AttractionStaffId' => null,
                        'PaymentMethod' => $request->payment_method,
                        'PaymentStatus' => $request->payment_method === 'pay_on_arrival' ? 'pending' : 'completed'
                    ]);
                } catch (\Exception $e) {
                    \Log::error('Error creating ticket: ' . $e->getMessage());
                    \Log::error('Item data: ' . json_encode($item));
                    return redirect()->back()->with('error', 'Error creating ticket: ' . $e->getMessage());
                }
            }
        }

        // Clear the cart after successful payment and ticket creation
        Session::forget('cart');
        
        return Redirect::route('cart.confirmation');
    }

    private function ensureCartItemHasTime($item)
    {
        if (!isset($item['time'])) {
            $item['time'] = '00:00:00';
        }
        return $item;
    }

    private function ensureCartHasTime($cart)
    {
        foreach ($cart as $slug => $item) {
            $cart[$slug] = $this->ensureCartItemHasTime($item);
        }
        return $cart;
    }
}
