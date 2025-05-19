@extends('layouts.app')

@section('title', 'Ticket Details - Massar')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center">
        <h1 class="text-3xl font-bold text-gray-600 mb-2">Ticket Details</h1>
        <a href="{{ route('tickets.index') }}" class="btn-primary flex items-center">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z" clip-rule="evenodd" />
            </svg>
            Back to Tickets
        </a>
    </div>
    <p class="text-gray-500">Booking reference: {{ substr($ticket->id, 0, 8) }}</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Ticket Information -->
    <div class="lg:col-span-2">
        <div class="card p-6 mb-6">
            <div class="flex justify-between items-center mb-6">
                <h2 class="text-2xl font-bold text-gray-600">{{ $attraction['title'] }}</h2>
                <span class="px-4 py-1 text-sm font-medium rounded-full {{ $ticket->state == 'valid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    {{ ucfirst($ticket->state ?? 'Pending') }}
                </span>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <div>
                    <img src="{{ $attraction['image'] }}" alt="{{ $attraction['title'] }}" class="w-full h-64 object-cover rounded-lg">
                </div>
                <div>
                    <div class="flex items-center gap-1 mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                        </svg>
                        <span class="text-gray-600">{{ $attraction['rating'] }} ({{ number_format($attraction['reviewCount']) }} reviews)</span>
                    </div>
                    
                    <div class="space-y-4">
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Description</h3>
                            <p class="text-gray-700">{{ $attraction['description'] }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Location</h3>
                            <p class="text-gray-700">{{ $attraction['location'] ?? 'Location information not available' }}</p>
                        </div>
                        
                        <div>
                            <h3 class="text-sm font-medium text-gray-500">Duration</h3>
                            <p class="text-gray-700">{{ $attraction['duration'] ?? 'Duration information not available' }}</p>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="border-t border-gray-200 pt-6">
                <h3 class="text-lg font-bold text-gray-600 mb-4">Ticket Information</h3>
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4">
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Booking ID</h4>
                        <p class="text-gray-700">{{ $ticket->id }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Ticket Type</h4>
                        <p class="text-gray-700">{{ $ticketType ? $ticketType->Title : 'Standard' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Number of Guests</h4>
                        <p class="text-gray-700">{{ $ticket->Quantity }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Visit Date</h4>
                        <p class="text-gray-700">{{ $ticket->VisitDate }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Visit Time</h4>
                        <p class="text-gray-700">{{ $ticket->TimeSlot ?? 'Not specified' }}</p>
                    </div>
                    <div>
                        <h4 class="text-sm font-medium text-gray-500">Booking Date</h4>
                        <p class="text-gray-700">{{ date('Y-m-d', strtotime($ticket->BookingTime)) }}</p>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="card p-6">
            <h3 class="text-lg font-bold text-gray-600 mb-4">Important Information</h3>
            <div class="space-y-4">
                <div>
                    <h4 class="font-medium text-gray-600">What to bring</h4>
                    <ul class="list-disc pl-5 text-gray-600 mt-2">
                        <li>Printed or mobile ticket</li>
                        <li>Valid ID</li>
                        <li>Comfortable walking shoes</li>
                        <li>Sun protection</li>
                        <li>Water bottle</li>
                    </ul>
                </div>
                
                <div>
                    <h4 class="font-medium text-gray-600">Cancellation Policy</h4>
                    <p class="text-gray-600 mt-2">
                        Cancellations made at least 48 hours before the scheduled visit date are eligible for a full refund.
                        No refunds will be given for cancellations made less than 48 hours before the scheduled visit.
                    </p>
                </div>
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
                    <span class="font-bold">Price per ticket:</span>
                    <span class="font-bold">{{ number_format($ticket->TotalCost / $ticket->Quantity, 2) }}£E</span>
                </div>
                
                <div class="flex justify-between text-lg font-bold">
                    <span>Total Cost:</span>
                    <span>{{ $ticket->TotalCost }}£E</span>
                </div>
            </div>
            
            <div class="border-t border-gray-200 pt-4 mb-4">
                <div class="flex flex-col space-y-2">
                    <div class="flex items-center text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>Instant confirmation</span>
                    </div>
                    <div class="flex items-center text-gray-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2 text-green-500" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" />
                        </svg>
                        <span>Mobile voucher accepted</span>
                    </div>
                </div>
            </div>
            
            <div class="p-4 bg-blue-50 rounded-lg text-blue-800 text-sm">
                <div class="flex items-center mb-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" />
                    </svg>
                    <span class="font-medium">Contact Information</span>
                </div>
                <p>If you have any questions about your ticket, please contact our support team:</p>
                <p class="mt-2">
                    <strong>Email:</strong> support@massar.com<br>
                    <strong>Phone:</strong> +20 123-456-7890
                </p>
            </div>
        </div>
    </div>
</div>

<div class="mt-12">
    <h2 class="text-2xl font-bold text-gray-600 mb-6">More Attractions You Might Like</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php
            $attractionController = new App\Http\Controllers\AttractionController();
            $allAttractions = $attractionController->getAttractions();
            $recommended = array_slice($allAttractions, 0, 3);
        @endphp
        
        @foreach($recommended as $rec)
            <div class="card">
                <div class="relative">
                    <img src="{{ $rec['image'] }}" alt="{{ $rec['title'] }}" class="w-full h-48 object-cover">
                    <div class="absolute top-4 left-4 bg-white/80 backdrop-blur-sm px-3 py-1 rounded-full">
                        <p class="text-gray-600 font-medium">
                            From {{ $rec['price'] }}£E<span class="text-sm">/person</span>
                        </p>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-xl font-bold text-gray-600">{{ $rec['title'] }}</h3>
                        <div class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="text-sm text-gray-600">{{ $rec['rating'] }}</span>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4 line-clamp-2">{{ $rec['description'] }}</p>
                    <a href="{{ route('attractions.show', $rec['slug']) }}" class="btn-primary w-full">View Details</a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
