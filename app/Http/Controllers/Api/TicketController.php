<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AttractionController;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Attraction;
use App\Models\TicketType;
use Illuminate\Support\Facades\Log;
// Illuminate\Support\Facades\Auth; // No longer needed if all Auth calls are replaced

class TicketController extends Controller
{
    /**
     * Get all tickets for the authenticated user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }

        $tickets = [];
        $total = 0;
        
        $attractionController = new AttractionController();
        $allAttractions = $attractionController->getAttractions();
        
        $userTickets = Ticket::where('TouristId', $user->id)->get();
        
        $attractionIds = $userTickets->pluck('Attraction')->toArray();
        $reviewStats = $attractionController->getMultipleAttractionReviewStats($attractionIds);
        
        foreach ($userTickets as $ticket) {
            $attraction = Attraction::find($ticket->Attraction);
            if ($attraction) {
                $attractionData = null;
                foreach ($allAttractions as $slug => $data) {
                    if ($data['id'] == $attraction->id) {
                        $attractionData = $data;
                        break;
                    }
                }
                
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
                    
                    if (isset($reviewStats[$attraction->id])) {
                        $attractionData['rating'] = $reviewStats[$attraction->id]['average_rating'];
                        $attractionData['reviewCount'] = $reviewStats[$attraction->id]['review_count'];
                    }
                    
                    $tickets[] = $attractionData;
                    $total += $ticket->TotalCost;
                }
            }
        }

        return response()->json([
            'success' => true,
            'data' => [
                'tickets' => $tickets,
                'total' => $total
            ]
        ]);
    }

    /**
     * Get a specific ticket by ID
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized',
            ], 401);
        }
        
        $ticket = Ticket::where('id', $id)
                        ->where('TouristId', $user->id)
                        ->first();
        
        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Ticket not found or does not belong to the user.',
            ], 404);
        }
        
        $attraction = Attraction::find($ticket->Attraction);
        
        if (!$attraction) {
            return response()->json([
                'success' => false,
                'message' => 'Attraction information not found for this ticket.',
            ], 404);
        }
        
        $attractionController = new AttractionController();
        $allAttractions = $attractionController->getAttractions();
        $reviewStats = $attractionController->getAttractionReviewStats($attraction->id);
        
        $attractionData = null;
        foreach ($allAttractions as $slug => $data) {
            if ($data['id'] == $attraction->id) {
                $attractionData = $data;
                break;
            }
        }
        
        if (!$attractionData) {
            return response()->json([
                'success' => false,
                'message' => 'Attraction details not found in the preloaded list.',
            ], 404);
        }
        
        $ticketType = TicketType::find($ticket->TicketTypesId);
        
        return response()->json([
            'success' => true,
            'data' => [
                'ticket' => [
                    'id' => $ticket->id,
                    'quantity' => $ticket->Quantity,
                    'booking_time' => $ticket->BookingTime,
                    'total_cost' => $ticket->TotalCost,
                    'visit_date' => $ticket->VisitDate,
                    'time_slot' => $ticket->TimeSlot,
                    'phone_number' => $ticket->PhoneNumber,
                    'state' => $ticket->state
                ],
                'attraction' => $attractionData,
                'ticket_type' => $ticketType ? [
                    'id' => $ticketType->id,
                    'title' => $ticketType->Title,
                    'description' => $ticketType->Description
                ] : null,
                'review_stats' => $reviewStats
            ]
        ]);
    }

    /**
     * Get all available ticket types
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTicketTypes()
    {
        $ticketTypes = TicketType::all();
        
        return response()->json([
            'success' => true,
            'data' => $ticketTypes
        ]);
    }
}

