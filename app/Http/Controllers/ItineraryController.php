<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attraction;
use App\Models\TicketType;
use App\Models\Itinerary;
use App\Models\ItineraryItem;
use App\Models\ItineraryType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ItineraryController extends Controller
{
    /**
     * Display the itinerary designer page
     */
    public function designer(Request $request, $uuid = null)
    {
        // Get attraction controller instance to use its methods
        $attractionController = new AttractionController();
        $allAttractions = $attractionController->getAttractions();
        
        // Get itinerary types
        $itineraryTypes = ItineraryType::all();
        
        // If no types exist, create default ones
        if ($itineraryTypes->isEmpty()) {
            $this->createDefaultItineraryTypes();
            $itineraryTypes = ItineraryType::all();
        }
        
        $itinerary = Itinerary::where('uuid', $uuid)->first();
        
        // If itinerary exists but doesn't belong to current user
        if ($itinerary && $itinerary->user_id !== Auth::id()) {
            return view('itinerary.copy-prompt', [
                'itinerary' => $itinerary,
                'categories' => $attractionController->getCategories(),
            ]);
        }
        
        // If itinerary doesn't exist, create a new one
        if (!$itinerary) {
            $itinerary = $this->getOrCreateItinerary();
        }
        
        // Get itinerary items grouped by day
        $itineraryItems = $this->getItineraryItemsByDay($itinerary, $allAttractions);
        
        // Calculate total cost, duration, and number of attractions
        $stats = $this->calculateItineraryStats($itineraryItems, $allAttractions);
        
        return view('itinerary.designer', [
            'attractions' => $allAttractions,
            'categories' => $attractionController->getCategories(),
            'itineraryTypes' => $itineraryTypes,
            'itinerary' => $itinerary,
            'itineraryItems' => $itineraryItems,
            'stats' => $stats,
        ]);
    }

    /**
     * Copy an existing itinerary and its items and link it to the current user
     * 
     * @param string $uuid UUID of the itinerary to copy
     * @return \Illuminate\Http\RedirectResponse
     */
    public function copyItinerary($uuid)
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // Find the original itinerary
        $originalItinerary = Itinerary::where('uuid', $uuid)->firstOrFail();
        
        // Create a new itinerary with the same details but new UUID
        $newItinerary = new Itinerary([
            'uuid' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'type_id' => $originalItinerary->type_id,
            'name' => 'Copy of ' . $originalItinerary->name,
            'description' => $originalItinerary->description,
            'public' => false, // Default to private for copied itineraries
        ]);
        
        $newItinerary->save();
        
        // Copy all itinerary items
        $originalItems = ItineraryItem::where('itinerary_id', $originalItinerary->uuid)->get();
        
        foreach ($originalItems as $originalItem) {
            $newItem = new ItineraryItem([
                'uuid' => (string) Str::uuid(),
                'itinerary_id' => $newItinerary->uuid,
                'attraction_id' => $originalItem->attraction_id,
                'date' => $originalItem->date,
                'time' => $originalItem->time,
                'quantity' => $originalItem->quantity,
                'TicketTypeId' => $originalItem->TicketTypeId,
                'position' => $originalItem->position,
            ]);
            
            $newItem->save();
        }
        
        return redirect()->route('itinerary.designer', $newItinerary->uuid)
            ->with('success', 'Itinerary copied successfully!');
    }
    /**
     * Add an attraction to the itinerary
     */
    public function addAttraction(Request $requestm, $itineraryUuid = null)
    {
        $validated = $request->validate([
            'attraction_id' => 'required|exists:attractions,id',
            'date' => 'required|date',
            'time' => 'nullable',
            'quantity' => 'required|integer|min:1',
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'day' => 'required|integer|min:1',
        ]);
        
        if ($itineraryUuid) {
            // If itinerary UUID is provided, find that specific itinerary
            $itinerary = Itinerary::where('uuid', $itineraryUuid)
                ->where('user_id', Auth::id())
                ->firstOrFail();
        } else {
            // If no UUID is provided, get or create a new itinerary
            $itinerary = $this->getOrCreateItinerary();
        }
        // Calculate position (get max position for the day and add 1)
        $maxPosition = ItineraryItem::where('itinerary_id', $itinerary->uuid)
            ->whereDate('date', Carbon::parse($validated['date']))
            ->max('position') ?? 0;
            
        // Create itinerary item
        $item = new ItineraryItem([
            'uuid' => (string) Str::uuid(),
            'itinerary_id' => $itinerary->uuid,
            'attraction_id' => $validated['attraction_id'],
            'date' => $validated['date'],
            'time' => null  ,
            'quantity' => $validated['quantity'],
            'TicketTypeId' => $validated['ticket_type_id'],
            'position' => $maxPosition + 1,
        ]);
        
        $item->save();
        
        return redirect()->route('itinerary.designer', $itinerary->uuid)
            ->with('success', 'Attraction added to your itinerary!');
    }
    
    /**
     * Update attraction in the itinerary
     */
    public function updateAttraction(Request $request, $uuid)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'nullable',
            'quantity' => 'required|integer|min:1',
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'day' => 'required|integer|min:1',
        ]);
        
        // Find the itinerary item
        $item = ItineraryItem::where('uuid', $uuid)
            ->where('itinerary_id', $this->getOrCreateItinerary()->uuid)
            ->firstOrFail();
            
        // Update the item
        $item->update([
            'date' => $validated['date'],
            'time' => $validated['time'],
            'quantity' => $validated['quantity'],
            'TicketTypeId' => $validated['ticket_type_id'],
        ]);
        
        return redirect()->route('itinerary.designer', $item->itinerary_id)
            ->with('success', 'Itinerary item updated!');
    }
    
    /**
     * Remove attraction from the itinerary
     */
    public function removeAttraction($uuid)
    {
        $item = ItineraryItem::where('uuid', $uuid)
            ->where('itinerary_id', $this->getOrCreateItinerary()->uuid)
            ->firstOrFail();
            
        $item->delete();
        
        return redirect()->route('itinerary.designer', $item->itinerary_id)
            ->with('success', 'Attraction removed from your itinerary!');
    }
    
    /**
     * Update itinerary details
     */
    public function updateItinerary(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'type_id' => 'required|exists:itinerary_types,id',
            'public' => 'boolean',
        ]);
        
        $itinerary = $this->getOrCreateItinerary();
        $itinerary->update($validated);
        
        return redirect()->route('itinerary.designer', $itinerary->uuid)
            ->with('success', 'Itinerary details updated!');
    }
    
    /**
     * Get or create an itinerary for the current user
     */
    private function getOrCreateItinerary($uuid = null)
{
    // If UUID is provided, try to find that specific itinerary
    if ($uuid) {
        $itinerary = Itinerary::where('uuid', $uuid)
            ->where('user_id', Auth::id())
            ->first();
            
        if ($itinerary) {
            return $itinerary;
        }
    }
    
    // If no UUID or itinerary not found, look for the user's most recent itinerary
    $itinerary = Itinerary::where('user_id', Auth::id())
        ->latest()
        ->first();
        
    // If still no itinerary, create a new one
    if (!$itinerary) {
        $typeId = ItineraryType::first()->id ?? $this->createDefaultItineraryTypes()->first()->id;
        
        $itinerary = new Itinerary([
            'uuid' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'type_id' => $typeId,
            'name' => 'My Trip ' . date('Y'),
            'description' => 'My custom Egypt itinerary',
            'public' => false,
        ]);
        
        $itinerary->save();
    }
    
    return $itinerary;
}

/**
 * Creates a new itinerary regardless of existing ones
 */
    private function addItinerary()
    {
        // Get the first itinerary type or create a default one
        $typeId = ItineraryType::first()->id ?? $this->createDefaultItineraryTypes()->first()->id;
        
        $itinerary = new Itinerary([
            'uuid' => (string) Str::uuid(),
            'user_id' => Auth::id(),
            'type_id' => $typeId,
            'name' => 'My Trip ' . date('Y'),
            'description' => 'My custom Egypt itinerary',
            'public' => false,
        ]);
        
        $itinerary->save();
        
        return $itinerary;
    }

    /**
     * Create a new itinerary and redirect to the designer
     * 
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createNewItinerary()
    {
        $newItinerary = $this->addItinerary();

        return redirect()->route('itinerary.designer', $newItinerary->uuid);
    }
    
    /**
     * Create default itinerary types
     * TODO: This should be managed in filament
     */
    private function createDefaultItineraryTypes()
    {
        $types = [
            'Weekend Trip',
            'Week-long Vacation',
            'Family Holiday',
            'Solo Adventure',
            'Cultural Exploration',
            'Custom'
        ];
        
        $createdTypes = collect();
        
        foreach ($types as $type) {
            $createdTypes->push(ItineraryType::create([
                'name' => $type,
            ]));
        }
        
        return $createdTypes;
    }
      /**
     * Group itinerary items by day
     */
    public function getItineraryItemsByDay($itinerary, $allAttractions)
    {
        $items = ItineraryItem::where('itinerary_id', $itinerary->uuid)
            ->orderBy('date')
            ->orderBy('position')
            ->get();
            
        $groupedItems = [];
        
        foreach ($items as $item) {
            // Calculate the day number (day 1, day 2, etc.)
            $firstDate = $items->min('date');
            $dayDiff = Carbon::parse($item->date)->diffInDays(Carbon::parse($firstDate)) + 1;
            
            if (!isset($groupedItems[$dayDiff])) {
                $groupedItems[$dayDiff] = [];
            }
            
            // Find attraction details
            $attractionSlug = null;
            foreach ($allAttractions as $slug => $attraction) {
                if ($attraction['id'] == $item->attraction_id) {
                    $attractionSlug = $slug;
                    break;
                }
            }
            
            if ($attractionSlug) {
                $attraction = $allAttractions[$attractionSlug];
                $attraction['quantity'] = $item->quantity;
                $attraction['date'] = $item->date;
                $attraction['time'] = $item->time;
                $attraction['uuid'] = $item->uuid;
                $attraction['ticket_type_id'] = $item->TicketTypeId;
                $attraction['subtotal'] = $attraction['price'] * $item->quantity;
                
                $groupedItems[$dayDiff][] = $attraction;
            }
        }
        
        return $groupedItems;
    }
      /**
     * Calculate itinerary stats (total cost, duration, number of attractions)
     */
    public function calculateItineraryStats($itineraryItems, $allAttractions)
    {
        $totalCost = 0;
        $totalAttractions = 0;
        $totalDuration = 0; // in hours
        
        foreach ($itineraryItems as $day => $items) {
            foreach ($items as $item) {
                $totalCost += $item['subtotal'];
                $totalAttractions++;
                
                // Add duration (estimate as 3 hours per attraction if not available)
                $durationMap = [
                    'short' => 3,
                    'medium' => 5,
                    'full_day' => 8,
                    'multi_day' => 24
                ];
                
                $durationType = $item['durationType'] ?? 'medium';
                $totalDuration += $durationMap[$durationType];
            }
        }
        
        return [
            'totalCost' => $totalCost,
            'totalAttractions' => $totalAttractions,
            'totalDuration' => $totalDuration,
            'durationText' => $this->formatDuration($totalDuration)
        ];
    }
      /**
     * Format duration in hours as human-readable text
     */
    private function formatDuration($hours)
    {
        if ($hours < 24) {
            return $hours . ' hours';
        } else {
            $days = floor($hours / 24);
            $remainingHours = $hours % 24;
            
            if ($remainingHours > 0) {
                return $days . ' day' . ($days > 1 ? 's' : '') . ' ' . $remainingHours . ' hour' . ($remainingHours > 1 ? 's' : '');
            } else {
                return $days . ' day' . ($days > 1 ? 's' : '');
            }
        }
    }
    
    /**
     * Display a listing of public itineraries
     */
    public function index(Request $request)
    {
        // Get attraction controller instance to use its methods
        $attractionController = new AttractionController();
        $allAttractions = $attractionController->getAttractions();
        
        // Get public itineraries with their relationships
        $query = Itinerary::with(['type', 'user', 'items.attraction', 'items.ticketType'])
            ->where('public', true)
            ->latest();
            
        // Filter by type if requested
        if ($request->has('type') && $request->type) {
            $query->where('type_id', $request->type);
        }
        
        // Filter by search query if requested
        if ($request->has('query') && $request->query) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->query . '%')
                  ->orWhere('description', 'like', '%' . $request->query . '%');
            });
        }
            
        $itineraries = $query->paginate(9);
        
        // Process each itinerary to calculate stats
        foreach ($itineraries as $itinerary) {
            // Group items by day for each itinerary
            $itinerary->groupedItems = $this->getItineraryItemsByDay($itinerary, $allAttractions);
            
            // Calculate stats for each itinerary
            $itinerary->stats = $this->calculateItineraryStats($itinerary->groupedItems, $allAttractions);
        }
        
        $itineraryTypes = ItineraryType::all();
        
        return view('itinerary.publicItineraries', [
            'itineraries' => $itineraries,
            'itineraryTypes' => $itineraryTypes,
            'categories' => $attractionController->getCategories(),
            'currentType' => $request->type,
            'query' => $request->query
        ]);
    }

    /**
     * Display a listing of user's itineraries
     */
    public function userItineraries(Request $request)
    {
        // Ensure user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }
        
        // Get attraction controller instance to use its methods
        $attractionController = new AttractionController();
        $allAttractions = $attractionController->getAttractions();
        
        // Get the current user's itineraries with their relationships
        $query = Itinerary::with(['type', 'user', 'items.attraction', 'items.ticketType'])
            ->where('user_id', Auth::id())
            ->latest();
            
        // Filter by type if requested
        if ($request->has('type') && $request->type) {
            $query->where('type_id', $request->type);
        }
        
        // Filter by search query if requested
        if ($request->has('query') && $request->query) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->query . '%')
                  ->orWhere('description', 'like', '%' . $request->query . '%');
            });
        }
            
        $itineraries = $query->paginate(9);
        
        // Process each itinerary to calculate stats
        foreach ($itineraries as $itinerary) {
            // Group items by day for each itinerary
            $itinerary->groupedItems = $this->getItineraryItemsByDay($itinerary, $allAttractions);
            
            // Calculate stats for each itinerary
            $itinerary->stats = $this->calculateItineraryStats($itinerary->groupedItems, $allAttractions);
        }
        
        $itineraryTypes = ItineraryType::all();
        
        return view('itinerary.myItineraries', [
            'itineraries' => $itineraries,
            'itineraryTypes' => $itineraryTypes,
            'categories' => $attractionController->getCategories(),
            'currentType' => $request->type,
            'query' => $request->query
        ]);
    }
    
    /**
     * Show a specific public itinerary
     */
    public function show($uuid)
    {
        // Get attraction controller instance to use its methods
        $attractionController = new AttractionController();
        $allAttractions = $attractionController->getAttractions();
        
        // Get the itinerary with its relationships
        $itinerary = Itinerary::with(['type', 'user', 'items.attraction', 'items.ticketType'])
            ->where('uuid', $uuid)
            ->firstOrFail();
            
        // Only show if public or belongs to current user
        if (!$itinerary->public && (!Auth::check() || $itinerary->user_id != Auth::id())) {
            abort(403, 'This itinerary is private.');
        }
        
        // Group items by day
        $groupedItems = $this->getItineraryItemsByDay($itinerary, $allAttractions);
        
        // Calculate stats
        $stats = $this->calculateItineraryStats($groupedItems, $allAttractions);
        
        return view('itinerary.show', [
            'itinerary' => $itinerary,
            'itineraryItems' => $groupedItems,
            'stats' => $stats,
            'categories' => $attractionController->getCategories()
        ]);
    }
}
