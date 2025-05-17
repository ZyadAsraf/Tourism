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
    public function designer(Request $request)
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
        
        // Get or create a default itinerary for the current user
        $itinerary = $this->getOrCreateItinerary();
        
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
     * Add an attraction to the itinerary
     */
    public function addAttraction(Request $request)
    {
        $validated = $request->validate([
            'attraction_id' => 'required|exists:attractions,id',
            'date' => 'required|date',
            'time' => 'nullable',
            'quantity' => 'required|integer|min:1',
            'ticket_type_id' => 'required|exists:ticket_types,id',
            'day' => 'required|integer|min:1',
        ]);
        
        // Get or create itinerary
        $itinerary = $this->getOrCreateItinerary();
        
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
            'time' => null,
            'quantity' => $validated['quantity'],
            'TicketTypeId' => $validated['ticket_type_id'],
            'position' => $maxPosition + 1,
        ]);
        
        $item->save();
        
        return redirect()->route('itinerary.designer')
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
        
        return redirect()->route('itinerary.designer')
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
        
        return redirect()->route('itinerary.designer')
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
        
        return redirect()->route('itinerary.designer')
            ->with('success', 'Itinerary details updated!');
    }
    
    /**
     * Get or create an itinerary for the current user
     */
    private function getOrCreateItinerary()
    {
        $itinerary = Itinerary::where('user_id', Auth::id())
            ->latest()
            ->first();
            
        if (!$itinerary) {
            // Get the first itinerary type or create a default one
            $typeId = ItineraryType::first()->id ?? $this->createDefaultItineraryTypes()->first()->id;
            
            $itinerary = new Itinerary([
                'uuid' => (string) Str::uuid(),
                'user_id' => Auth::id(),
                'type_id' => $typeId,
                'name' => 'My Aswan Trip ' . date('Y'),
                'description' => 'My custom Egypt itinerary',
                'public' => false,
            ]);
            
            $itinerary->save();
        }
        
        return $itinerary;
    }
    
    /**
     * Create default itinerary types
     */
    private function createDefaultItineraryTypes()
    {
        $types = [
            'Weekend Trip',
            'Week-long Vacation',
            'Family Holiday',
            'Solo Adventure',
            'Cultural Exploration',
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
    private function getItineraryItemsByDay($itinerary, $allAttractions)
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
    private function calculateItineraryStats($itineraryItems, $allAttractions)
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
}
