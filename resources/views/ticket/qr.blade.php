@extends('layouts.app')

@section('title', 'Ticket QR Code - Massar')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-600 mb-2">Ticket QR Code</h1>
        <a href="{{ route('tickets.show', $ticket->id) }}" class="btn-primary flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Ticket
        </a>
    </div>
    <p class="text-gray-500">Booking reference: {{ substr($ticket->id, 0, 8) }}</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- QR Code Section -->
    <div class="lg:col-span-2">
        <div class="card p-8 text-center">
            <h2 class="text-2xl font-bold text-gray-600 mb-6">Scan this QR code</h2>
            
            <div class="mb-6">
                <img src="{{ $qrData['qr_image_url'] }}" alt="Ticket QR Code" class="mx-auto w-64 h-64 border border-gray-200 rounded-lg">
            </div>
            
            <div class="text-gray-600 mb-6">
                <p>Present this QR code to the attraction staff for entry.</p>
                <p class="mt-2">Valid for {{ $ticket->Quantity }} {{ $ticket->Quantity > 1 ? 'guests' : 'guest' }}.</p>
                <p class="mt-2">Visit date: {{ $ticket->VisitDate }}</p>
                @if($ticket->TimeSlot)
                    <p>Time slot: {{ $ticket->TimeSlot }}</p>
                @endif
            </div>
            
            <div class="p-4 bg-blue-50 rounded-lg text-blue-800 text-sm">
                <div class="flex items-center mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">Important</span>
                </div>
                <p>This QR code is unique to your booking and cannot be reused.</p>
                <p class="mt-2">Please do not share this QR code with others.</p>
            </div>
        </div>
    </div>
    
    <!-- Ticket Summary Sidebar -->
    <div class="lg:col-span-1">
        <div class="card p-6 sticky top-4">
            <h2 class="text-xl font-bold mb-4 text-gray-600">Ticket Summary</h2>
            
            <div class="space-y-4 mb-6">
                <div class="flex justify-between">
                    <span class="text-gray-600">Attraction:</span>
                    <span class="text-gray-600 font-medium">{{ $attraction['title'] }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Date:</span>
                    <span class="text-gray-600">{{ $ticket->VisitDate }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Time:</span>
                    <span class="text-gray-600">{{ $ticket->TimeSlot ?? 'Not specified' }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Ticket Type:</span>
                    <span class="text-gray-600">{{ $ticketType ? $ticketType->Title : 'Standard' }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Guests:</span>
                    <span class="text-gray-600">{{ $ticket->Quantity }}</span>
                </div>
                
                <div class="border-t border-gray-200 pt-4 flex justify-between">
                    <span class="font-bold">Total Cost:</span>
                    <span class="font-bold">{{ $ticket->TotalCost }}£E</span>
                </div>
            </div>
            
            <div class="p-4 bg-gray-50 rounded-lg text-gray-600 text-sm">
                <div class="flex items-center mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">Status</span>
                </div>
                <p>Ticket Status: <span class="font-medium {{ $ticket->state == 'valid' ? 'text-green-600' : 'text-red-600' }}">{{ ucfirst($ticket->state ?? 'Pending') }}</span></p>
                <p class="mt-2">Booking ID: {{ substr($ticket->id, 0, 8) }}</p>
            </div>
        </div>
    </div>
</div>

<div class="mt-8 text-center">
    <div class="card p-6">
        <h3 class="text-lg font-bold text-gray-600 mb-4">QR Code Usage Instructions</h3>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm text-gray-600">
            <div class="flex flex-col items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-2">
                    <span class="text-blue-600 font-bold text-xl">1</span>
                </div>
                <p class="font-medium">Arrive at Attraction</p>
                <p class="text-center">Visit the attraction on your scheduled date</p>
            </div>
            <div class="flex flex-col items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-2">
                    <span class="text-blue-600 font-bold text-xl">2</span>
                </div>
                <p class="font-medium">Present QR Code</p>
                <p class="text-center">Show this QR code to the staff at the entrance</p>
            </div>
            <div class="flex flex-col items-center">
                <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mb-2">
                    <span class="text-blue-600 font-bold text-xl">3</span>
                </div>
                <p class="font-medium">Enjoy Your Visit</p>
                <p class="text-center">Your ticket will be validated and you can enter</p>
            </div>
        </div>
    </div>
</div>
@endsection
