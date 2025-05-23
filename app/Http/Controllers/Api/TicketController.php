<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\AttractionController;
use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Attraction;
use App\Models\TicketType;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use App\Models\User;
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
        $ticketData = [
            'id' => $ticket->id,
            'touristId' => $ticket->TouristId,
            'attractionId' => $ticket->Attraction,
            'quantity' => $ticket->Quantity,
            'visitDate' => $ticket->VisitDate,
            'timeSlot' => $ticket->TimeSlot,
            'generated' => now()->timestamp
        ];
        
        // Encrypt the ticket data
        $encryptedData = $this->encryptTicketData($ticketData);

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
                'attraction' => $encryptedData,
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

    /**
     * Verify a ticket belongs to a user and has the correct date
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyTicket(Request $request)
    {
        try {
            // Validate the request
            $validated = $request->validate([
                'ticket_data' => 'required|string',
                'user_data' => 'required|string',
            ]);
            
            // Decode JSON strings
            $ticketData = json_decode($validated['ticket_data'], true);
            $userData = json_decode($validated['user_data'], true);
            
            // Check if JSON was decoded successfully
            if (!$ticketData || !$userData) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid JSON data provided',
                ], 400);
            }
            
            // Validate required fields
            if (!isset($ticketData['id']) || !isset($userData['user_id']) || !isset($ticketData['visit_date'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Missing required fields in provided data',
                ], 400);
            }
            
            // Find the ticket
            $ticket = Ticket::find($ticketData['id']);
            
            if (!$ticket) {
                return response()->json([
                    'success' => false,
                    'message' => 'Ticket not found',
                ], 404);
            }
            
            // Verify ticket belongs to the user
            $isValidUser = ($ticket->TouristId == $userData['user_id']);
            
            // Verify the date matches
            $isValidDate = ($ticket->VisitDate == $ticketData['visit_date']);
            
            // Get additional information for the response
            $attraction = Attraction::find($ticket->Attraction);
            $ticketType = TicketType::find($ticket->TicketTypesId);
            
            return response()->json([
                'success' => true,
                'data' => [
                    'is_valid' => ($isValidUser && $isValidDate),
                    'valid_user' => $isValidUser,
                    'valid_date' => $isValidDate,
                    'ticket_details' => [
                        'id' => $ticket->id,
                        'quantity' => $ticket->Quantity,
                        'visit_date' => $ticket->VisitDate,
                        'time_slot' => $ticket->TimeSlot,
                        'state' => $ticket->state,
                        'attraction_name' => $attraction ? $attraction->Name : 'Unknown',
                        'ticket_type' => $ticketType ? $ticketType->Title : 'Standard'
                    ]
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('Ticket verification error: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'An error occurred while verifying the ticket: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function validateScannedTicket(Request $request)
    {
        // Validate request
        $validated = $request->validate([
            'qr_data' => 'required|string',
        ]);
        
        // Send the encrypted data to the validation endpoint
        $response = Http::post(route('api.validate-ticket'), [
            'encrypted_data' => $validated['qr_data']
        ]);
        
        // Return the validation result
        return $response->json();
    }
    
    /**
     * Encrypt ticket data
     */
    private function encryptTicketData($ticketData)
    {
        // Convert to JSON and encrypt
        $jsonData = json_encode($ticketData);
        return Crypt::encryptString($jsonData);
    }
    
    /**
     * Decrypt ticket data
     */
    private function decryptTicketData($encryptedString)
    {
        try {
            $jsonData = Crypt::decryptString($encryptedString);
            return json_decode($jsonData, true);
        } catch (\Exception $e) {
            Log::error('Ticket decryption failed: ' . $e->getMessage());
            return null;
        }
    }
    
    /**
     * API endpoint to validate a ticket
     */
    public function validateTicket(Request $request)
    {
        $user = User::find($request->user()->id);
        $hasRole = $user->hasRole('Attraction_Staff');

        if (!$hasRole) {
            return response()->json([
                'valid' => false,
                'message' => 'Unauthorized: Staff role required'
            ], 403);
        }

        // Validate request
        $validated = $request->validate([
            'encrypted_data' => 'required|string',
        ]);
        
        $encryptedData = $validated['encrypted_data'];
        
        // Decrypt the data
        $ticketData = $this->decryptTicketData($encryptedData);
        
        // Check if decryption failed (invalid format)
        if (!$ticketData) {
            return response()->json([
                'valid' => false,
                'message' => 'Invalid ticket format'
            ], 400);
        }
        
        // Find the ticket in the database
        $ticket = Ticket::find($ticketData['id']);
        
        // Check if ticket exists
        if (!$ticket) {
            return response()->json([
                'valid' => false,
                'message' => 'Ticket not found'
            ], 404);
        }

        // NEW: Check if the staff member is assigned to this attraction
        if (!$user->canVerifyAttractionTickets($ticket->Attraction)) {
            return response()->json([
                'valid' => false,
                'message' => 'Access denied: You are not assigned to this attraction'
            ], 403);
        }
        
        // Check if ticket is valid
        if ($ticket->state !== 'valid') {
            return response()->json([
                'valid' => false,
                'message' => 'Ticket already used or invalid'
            ], 400);
        }
        
        // Check if ticket is for the correct date
        $visitDate = \Carbon\Carbon::parse($ticket->VisitDate);
        $today = \Carbon\Carbon::today();

        if (!$visitDate->isToday()) {
            return response()->json([
                'valid' => false,
                'message' => 'Ticket is not valid for today'
            ], 400);
        }
        
        // If we get here, the ticket is valid
        // Mark the ticket as used
        
        $ticket->attractionStaffId = $request->user()->id; // Assuming the user is the attraction staff
        $ticket->state = 'used';
        $ticket->save();
        
        return response()->json([
            'valid' => true,
            'guestsAllowed' => $ticket->Quantity,
            'guestType' => $ticket->TicketTypesId,
            'message' => 'Valid ticket'
        ], 200);
    }
}

