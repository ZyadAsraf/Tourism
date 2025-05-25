<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Ticket;
use App\Models\Attraction;
use App\Models\TicketType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Crypt;

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
        
        // Generate encrypted QR code data for the ticket
        $qrData = $this->generateQRData($ticket);
        
        return view('ticket.show', [
            'ticket' => $ticket,
            'attraction' => $attractionData,
            'ticketType' => $ticketType,
            'reviewStats' => $reviewStats,
            'qrData' => $qrData,
            'categories' => $attractionController->getCategories()
        ]);
    }
    
    /**
     * Generate QR code data for a ticket
     */
    public function generateQRData($ticket)
    {
        // Create ticket data object with only necessary information
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
        
        // Generate QR code URL using a QR code service
        $qrImageUrl = 'https://api.qrserver.com/v1/create-qr-code/?size=300x300&data=' . urlencode($encryptedData);
        
        return [
            'encrypted_data' => $encryptedData,
            'qr_image_url' => $qrImageUrl
        ];
    }
    
    /**
     * Display QR code for a ticket
     */
    public function showQR($id)
    {
        // Check if the user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Please login to view ticket QR code.');
        }
        
        $ticket = Ticket::where('id', $id)
                        ->where('TouristId', Auth::id())
                        ->first();
        
        if (!$ticket) {
            return redirect()->route('tickets.index')->with('error', 'Ticket not found.');
        }
        
        // Generate QR code data
        $qrData = $this->generateQRData($ticket);
        
        // Get attraction details
        $attraction = Attraction::find($ticket->Attraction);
        
        if (!$attraction) {
            return redirect()->route('tickets.index')->with('error', 'Attraction information not found.');
        }
        
        // Get attraction details from AttractionController
        $attractionController = new AttractionController();
        $allAttractions = $attractionController->getAttractions();
        
        // Find attraction data
        $attractionData = null;
        foreach ($allAttractions as $slug => $data) {
            if ($data['id'] == $attraction->id) {
                $attractionData = $data;
                break;
            }
        }
        
        $ticketType = TicketType::find($ticket->TicketTypesId);
        
        return view('ticket.qr', [
            'ticket' => $ticket,
            'attraction' => $attractionData,
            'ticketType' => $ticketType,
            'qrData' => $qrData,
            'categories' => $attractionController->getCategories()
        ]);
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
}
