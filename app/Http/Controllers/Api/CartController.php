<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AttractionController;
use Illuminate\Http\Request;
use App\Models\Attraction;
use App\Models\CartItem;
use App\Models\Ticket;
use App\Models\TicketType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CartController extends Controller
{
    /**
     * Get all items in the user's cart
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        // Check authentication
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
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
                // Try to find attraction data by slug or AttractionName
                $attractionSlug = $attraction->slug ?? Str::slug($attraction->AttractionName) ?? null;
                $attractionData = null;
                
                // Check direct match
                if ($attractionSlug && isset($allAttractions[$attractionSlug])) {
                    $attractionData = $allAttractions[$attractionSlug];
                } else {
                    // Search through attractions for a match
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
                    $attractionData['cart_item_id'] = $item->id;
                    
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

        return response()->json([
            'success' => true,
            'data' => [
                'attractions' => $attractions,
                'total' => $total,
            ]
        ]);
    }

    /**
     * Add an attraction to the cart
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function add(Request $request)
    {
        // Check authentication
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }
        
        // Validate request
        $validated = $request->validate([
            'attraction_id' => 'required|exists:attractions,id',
            'date' => 'required|date',
            'time' => 'required|string',
            'quantity' => 'required|integer|min:1',
            'ticket_type_id' => 'required|exists:ticket_types,id',
        ]);
        
        // Find the attraction
        $attraction = Attraction::find($validated['attraction_id']);
        
        if (!$attraction) {
            return response()->json([
                'success' => false,
                'message' => 'Attraction not found',
            ], 404);
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
            $cartItem = CartItem::create([
                'user_id' => Auth::id(),
                'attraction_id' => $attraction->id,
                'ticket_type_id' => $validated['ticket_type_id'],
                'quantity' => $validated['quantity'],
                'date' => $validated['date'],
                'time' => $validated['time'],
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'Attraction added to cart',
            'data' => [
                'cart_item' => $cartItem
            ]
        ]);
    }
    
    /**
     * Update cart item quantity
     *
     * @param Request $request
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id)
    {
        // Check authentication
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        // Validate request
        $validated = $request->validate([
            'date' => 'sometimes|date',
            'time' => 'sometimes|string',
            'quantity' => 'sometimes|integer|min:1',
            'ticket_type_id' => 'sometimes|exists:ticket_types,id',
        ]);

        $cartItem = CartItem::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();
        
        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found',
            ], 404);
        }
        
        // Update only the fields that are present in the request
        if ($request->has('date')) {
            $cartItem->date = $validated['date'];
        }
        if ($request->has('time')) {
            $cartItem->time = $validated['time'];
        }
        if ($request->has('quantity')) {
            if ($validated['quantity'] > 0) {
                $cartItem->quantity = $validated['quantity'];
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'Quantity must be greater than zero',
                ], 400);
            }
        }
        if ($request->has('ticket_type_id')) {
            $cartItem->ticket_type_id = $validated['ticket_type_id'];
        }
        
        $cartItem->save();
        
        return response()->json([
            'success' => true,
            'message' => 'Cart updated',
            'data' => [
                'cart_item' => $cartItem
            ]
        ]);
    }

    /**
     * Remove an item from the cart
     *
     * @param int $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function remove($id)
    {
        // Check authentication
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $cartItem = CartItem::where('id', $id)
            ->where('user_id', Auth::id())
            ->first();
        
        if (!$cartItem) {
            return response()->json([
                'success' => false,
                'message' => 'Cart item not found',
            ], 404);
        }
        
        $cartItem->delete();
        
        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart'
        ]);
    }

    /**
     * Clear the entire cart
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function clear()
    {
        // Check authentication
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        CartItem::where('user_id', Auth::id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart has been cleared'
        ]);
    }
    
    /**
     * Process checkout
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkout(Request $request)
    {
        // Check authentication
        if (!Auth::check()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        // Validate request
        $validated = $request->validate([
            'PhoneNumber' => 'required|string',
            'state' => 'required|string',
        ]);

        // Get cart items
        $cartItems = CartItem::where('user_id', Auth::id())->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty',
            ], 400);
        }
        
        $createdTickets = [];
        $ticketsCreated = 0;
        
        // Create a ticket for each cart item
        foreach ($cartItems as $item) {
            $attraction = Attraction::find($item->attraction_id);
            if (!$attraction) {
                continue; // Skip this item
            }
            
            // Get price from attraction model
            $currentPrice = $attraction->EntryFee;
            
            // Create ticket
            $ticket = Ticket::create([
                'TouristId' => Auth::id(),
                'Attraction' => $item->attraction_id,
                'TicketTypesId' => $item->ticket_type_id, 
                'Quantity' => $item->quantity,
                'BookingTime' => now(),
                'TotalCost' => $currentPrice * $item->quantity,
                'VisitDate' => $item->date,
                'TimeSlot' => $item->time,
                'PhoneNumber' => $validated['PhoneNumber'],
                'state' => $validated['state'],
            ]);
            
            $createdTickets[] = $ticket;
            $ticketsCreated++;
        }
        
        // Clear the cart after successful checkout if tickets were created
        if ($ticketsCreated > 0) {
            CartItem::where('user_id', Auth::id())->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Checkout successful',
                'data' => [
                    'tickets_created' => $ticketsCreated,
                    'tickets' => $createdTickets
                ]
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => 'Could not process checkout',
            ], 400);
        }
    }
    
    /**
     * Get cart count
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCount()
    {
        if (Auth::check()) {
            $count = CartItem::where('user_id', Auth::id())->count();
            return response()->json([
                'success' => true,
                'data' => [
                    'count' => $count
                ]
            ]);
        }
        
        return response()->json([
            'success' => false,
            'message' => 'Unauthorized',
        ], 401);
    }
}
