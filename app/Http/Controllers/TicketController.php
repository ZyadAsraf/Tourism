<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Attraction;
use App\Models\TicketType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TicketController extends Controller
{
    /**
     * Display a listing of the user's tickets
     */
    public function index()
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view your tickets.');
        }

        $tickets = [];
        $total = 0;
        
        // Create an instance of AttractionController
        $attractionController = new AttractionController();
        $allAttractions = $attractionController->getAttractions();
        
        // Get tickets from database for the current user
        $userTickets = Ticket::where('TouristId', Auth::id())->get();
        
        // Get attraction IDs for batch review stats retrieval
        $attractionIds = $userTickets->pluck('Attraction')->toArray();
        $reviewStats = $attractionController->getMultipleAttractionReviewStats($attractionIds);
        
        foreach ($userTickets as $ticket) {
            $attraction = Attraction::find($ticket->Attraction);
            if ($attraction) {
                // Try to find attraction data by AttractionId
                $attractionData = null;
                
                // Search through attractions for a match
                foreach ($allAttractions as $slug => $data) {
                    if ($data['id'] == $attraction->id) {
                        $attractionData = $data;
                        break;
                    }
                }
                
                // If we found the attraction data, add it to the tickets array
                if ($attractionData) {
                    $ticketType = TicketType::find($ticket->TicketTypesId);
                    
                    $attractionData['quantity'] = $ticket->Quantity;
                    $attractionData['date'] = $ticket->VisitDate;
                    $attractionData['time'] = $ticket->TimeSlot ?? 'Not specified';
                    $attractionData['ticket_type'] = $ticketType ? $ticketType->Title : 'Standard';
                    $attractionData['ticket_id'] = $ticket->id;
                    $attractionData['phone'] = $ticket->PhoneNumber;
                    $attractionData['subtotal'] = $ticket->TotalCost;
                    $attractionData['booking_time'] = $ticket->BookingTime;
                    $attractionData['state'] = $ticket->state;
                    
                    // Add review statistics
                    if (isset($reviewStats[$attraction->id])) {
                        $attractionData['rating'] = $reviewStats[$attraction->id]['average_rating'];
                        $attractionData['reviewCount'] = $reviewStats[$attraction->id]['review_count'];
                    }
                    
                    $tickets[] = $attractionData;
                    $total += $ticket->TotalCost;
                }
            }
        }

        return view('ticket.index', [
            'tickets' => $tickets,
            'total' => $total,
            'categories' => $attractionController->getCategories()
        ]);
    }

    /**
     * Display the specified ticket
     */
    public function show($id)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view ticket details.');
        }
        
        $ticket = Ticket::where('id', $id)
                        ->where('TouristId', Auth::id())
                        ->first();
        
        if (!$ticket) {
            return redirect()->route('tickets.index')->with('error', 'Ticket not found.');
        }
        
        $attraction = Attraction::find($ticket->Attraction);
        
        if (!$attraction) {
            return redirect()->route('tickets.index')->with('error', 'Attraction information not found.');
        }
        
        // Get attraction details from AttractionController
        $attractionController = new AttractionController();
        $allAttractions = $attractionController->getAttractions();
        
        // Get review statistics for this attraction
        $reviewStats = $attractionController->getAttractionReviewStats($attraction->id);
        
        // Find attraction data
        $attractionData = null;
        foreach ($allAttractions as $slug => $data) {
            if ($data['id'] == $attraction->id) {
                $attractionData = $data;
                break;
            }
        }
        
        if (!$attractionData) {
            return redirect()->route('tickets.index')->with('error', 'Attraction details not found.');
        }
        
        $ticketType = TicketType::find($ticket->TicketTypesId);
        
        return view('ticket.show', [
            'ticket' => $ticket,
            'attraction' => $attractionData,
            'ticketType' => $ticketType,
            'reviewStats' => $reviewStats,
            'categories' => $attractionController->getCategories()
        ]);
    }
}
