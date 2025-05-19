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
use App\Http\Controllers\AttractionController;

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
        
        // If no types exist, create default ones TODO:remove this
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
        
        // Get itinerary items grouped by day with date information
        $itineraryData = $this->getItineraryItemsByDay($itinerary, $allAttractions);
        
        // Calculate total cost, duration, and number of attractions
        $stats = $this->calculateItineraryStats($itineraryData, $allAttractions);
        
        return view('itinerary.designer', [
            'attractions' => $allAttractions,
            'categories' => $attractionController->getCategories(),
            'itineraryTypes' => $itineraryTypes,
            'itinerary' => $itinerary,
            'itineraryItems' => $itineraryData['items'] ?? [],
            'itineraryDays' => $itineraryData['days'] ?? [],
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
    public function addAttraction(Request $request, $itineraryUuid = null)
    {
        // Debug information
        \Log::info('Add Attraction Request', [
            'day' => $request->day,
            'attraction_id' => $request->attraction_id,
            'date' => $request->date,
            'itinerary_uuid' => $request->itinerary_uuid
        ]);
        
        $validated = $request->validate([
            'attraction_id' => 'required|exists:attractions,id',
            'date' => 'required|date',
            'time' => 'nullable',
            'quantity' => 'required|integer|min:1',
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'day' => 'required|integer|min:1',
            'itinerary_uuid' => 'nullable|string|exists:itineraries,uuid',
        ]);
        
        if (isset($validated['itinerary_uuid']) && !empty($validated['itinerary_uuid'])) {
            // If itinerary UUID is provided in the form
            $itinerary = Itinerary::where('uuid', $validated['itinerary_uuid'])
                ->where('user_id', Auth::id())
                ->firstOrFail();
        } else if ($itineraryUuid) {
            // If itinerary UUID is provided in the route
            $itinerary = Itinerary::where('uuid', $itineraryUuid)
                ->where('user_id', Auth::id())
                ->firstOrFail();
        } else {
            // If no UUID is provided, get or create a new itinerary
            $itinerary = $this->getOrCreateItinerary();
        }
        
        // Get the first date in the itinerary or use the provided date as a starting point
        $firstDate = ItineraryItem::where('itinerary_id', $itinerary->uuid)->min('date');
        $dayOffset = $validated['day'] - 1; // Convert day number to day offset (0-based)
        
        // If there's a first date, calculate the date based on the day offset
        // If not, use the provided date
        if ($firstDate && $validated['day'] > 1) {
            $date = Carbon::parse($firstDate)->addDays($dayOffset)->format('Y-m-d');
        } else if ($validated['day'] === 1) {
            // If it's day 1 and there are existing items, keep the first date consistent
            $date = $firstDate ?: $validated['date'];
        } else {
            $date = $validated['date'];
        }
        
        // Calculate position (get max position for the day and add 1)
        $maxPosition = ItineraryItem::where('itinerary_id', $itinerary->uuid)
            ->whereDate('date', $date)
            ->max('position') ?? 0;
            
        // Create itinerary item
        $item = new ItineraryItem([
            'uuid' => (string) Str::uuid(),
            'itinerary_id' => $itinerary->uuid,
            'attraction_id' => $validated['attraction_id'],
            'date' => $date,
            'time' => null,
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
        $itinerary = $this->getOrCreateItinerary();
        $item = ItineraryItem::where('uuid', $uuid)
            ->where('itinerary_id', $itinerary->uuid)
            ->firstOrFail();
        
        // Get all items in this itinerary to determine dates
        $items = ItineraryItem::where('itinerary_id', $itinerary->uuid)->get();
        $firstDate = $items->min('date');
        
        // Calculate the appropriate date based on the day number
        if ($firstDate && $validated['day'] > 1) {
            $dayOffset = $validated['day'] - 1;
            $date = Carbon::parse($firstDate)->addDays($dayOffset)->format('Y-m-d');
        } else if ($validated['day'] === 1) {
            $date = $firstDate ?: $validated['date'];
        } else {
            $date = $validated['date'];
        }
            
        // Update the item
        $item->update([
            'date' => $date,
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
     * Group itinerary items by day with date information
     */
    public function getItineraryItemsByDay($itinerary, $allAttractions)
    {
        $items = ItineraryItem::where('itinerary_id', $itinerary->uuid)
            ->orderBy('date')
            ->orderBy('position')
            ->get();
            
        if ($items->isEmpty()) {
            return [
                'days' => [],
                'items' => []
            ];
        }
            
        $groupedItems = [];
        $daysInfo = [];
        
        // First, get all unique dates
        $uniqueDates = $items->pluck('date')->unique()->sort()->values();
        
        // Debug info
        \Illuminate\Support\Facades\Log::info('Itinerary Items', [
            'count' => count($items),
            'days' => count($uniqueDates)
        ]);
        
        // First, organize items by their actual dates
        $dateGroupedItems = [];
        foreach ($items as $item) {
            $itemDate = $item->date;
            
            if (!isset($dateGroupedItems[$itemDate])) {
                $dateGroupedItems[$itemDate] = [];
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
                
                $dateGroupedItems[$itemDate][] = $attraction;
            }
        }
        
        // Now map date groups to day numbers (1, 2, 3, etc.) and include date information
        foreach ($uniqueDates as $index => $date) {
            $dayNumber = $index + 1; // Sequential days, no gaps
            $carbonDate = Carbon::parse($date);
            
            $daysInfo[$dayNumber] = [
                'date' => $date,
                'formatted_date' => $carbonDate->format('M d, Y'), // Format: Jan 15, 2025
                'day_of_week' => $carbonDate->format('l'), // Format: Monday, Tuesday, etc.
                'day_number' => $dayNumber
            ];
            
            $groupedItems[$dayNumber] = $dateGroupedItems[$date];
        }
        
        return [
            'days' => $daysInfo,
            'items' => $groupedItems
        ];
    }
    
    /**
     * Calculate itinerary stats (total cost, duration, number of attractions)
     */
    public function calculateItineraryStats($itineraryData, $allAttractions)
    {
        $totalCost = 0;
        $totalAttractions = 0;
        $totalDuration = 0; // in hours
        
        // If we receive the new structure with 'items' key, use that
        $itineraryItems = isset($itineraryData['items']) ? $itineraryData['items'] : $itineraryData;
        
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
        
        // Count the number of unique days in the itinerary
        $totalDays = isset($itineraryData['days']) ? count($itineraryData['days']) : count($itineraryItems);
        return [
            'totalCost' => $totalCost,
            'totalAttractions' => $totalAttractions,
            'totalDuration' => $totalDuration,
            'totalDays' => $totalDays,
            'durationText' => $this->formatDuration($totalDuration)
        ];
    }
    
    /**
     * Format duration in hours as human-readable text
     */
    private function formatDuration($hours)
    {
        // Use the actual days count from the stats instead of calculating from hours
        if ($hours < 8) {
            return $hours . ' hours';
        } else {
            // Convert to full days plus hours format
            $days = ceil($hours / 8); // Most attractions are visited during ~8 hour days
            
            if ($days == 1) {
                return '1 day';
            } else {
                return $days . ' days';
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
            // Group items by day for each itinerary with date information
            $itineraryData = $this->getItineraryItemsByDay($itinerary, $allAttractions);
            
            // Store both items and days info
            $itinerary->groupedItems = $itineraryData['items'] ?? [];
            $itinerary->daysInfo = $itineraryData['days'] ?? [];
            
            // Calculate stats for each itinerary
            $itinerary->stats = $this->calculateItineraryStats($itineraryData, $allAttractions);
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
            // Group items by day for each itinerary with date information
            $itineraryData = $this->getItineraryItemsByDay($itinerary, $allAttractions);
            
            // Store both items and days info
            $itinerary->groupedItems = $itineraryData['items'] ?? [];
            $itinerary->daysInfo = $itineraryData['days'] ?? [];
            
            // Calculate stats for each itinerary
            $itinerary->stats = $this->calculateItineraryStats($itineraryData, $allAttractions);
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
        
        // Group items by day with date information
        $itineraryData = $this->getItineraryItemsByDay($itinerary, $allAttractions);
        
        // Calculate stats
        $stats = $this->calculateItineraryStats($itineraryData, $allAttractions);
        
        return view('itinerary.show', [
            'itinerary' => $itinerary,
            'itineraryItems' => $itineraryData['items'] ?? [],
            'itineraryDays' => $itineraryData['days'] ?? [],
            'stats' => $stats,
            'categories' => $attractionController->getCategories()
        ]);
    }
}
