@extends('layouts.app')

@section('title', 'My Tickets - Massar')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-600 mb-2">My Tickets</h1>
    <p class="text-gray-500">View all your purchased tickets</p>
</div>

@if(count($tickets) > 0)
    <div class="grid grid-cols-1 gap-8">
        <div class="col-span-1">
            @foreach($tickets as $ticket)
                <div class="card p-4 mb-4 flex flex-col md:flex-row gap-4">
                    <div class="w-full md:w-1/4">
                        <img src="{{ $ticket['image'] }}" alt="{{ $ticket['title'] }}" class="w-full h-32 object-cover rounded-lg">
                    </div>
                    <div class="w-full md:w-3/4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold text-gray-600">{{ $ticket['title'] }}</h3>
                            <span class="px-3 py-1 text-xs font-medium rounded-full {{ $ticket['state'] == 'valid' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ ucfirst($ticket['state'] ?? 'Pending') }}
                            </span>
                        </div>
                        <div class="flex items-center gap-1 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="text-sm text-gray-600">{{ $ticket['rating'] }} ({{ number_format($ticket['reviewCount']) }})</span>
                        </div>
                        <p class="text-gray-600 mb-4 line-clamp-2">{{ $ticket['description'] }}</p>
                        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4 mb-4">
                            <div class="text-sm text-gray-500">
                                <span class="font-medium">Booking ID:</span>
                                <div class="text-gray-600">{{ substr($ticket['ticket_id'], 0, 8) }}</div>
                            </div>
                            <div class="text-sm text-gray-500">
                                <span class="font-medium">Visit Date:</span>
                                <div class="text-gray-600">{{ $ticket['date'] }}</div>
                            </div>
                            <div class="text-sm text-gray-500">
                                <span class="font-medium">Time:</span>
                                <div class="text-gray-600">{{ $ticket['time'] }}</div>
                            </div>
                            <div class="text-sm text-gray-500">
                                <span class="font-medium">Ticket Type:</span>
                                <div class="text-gray-600">{{ $ticket['ticket_type'] }}</div>
                            </div>
                            <div class="text-sm text-gray-500">
                                <span class="font-medium">Guests:</span>
                                <div class="text-gray-600">{{ $ticket['quantity'] }}</div>
                            </div>
                            <div class="text-sm text-gray-500">
                                <span class="font-medium">Phone:</span>
                                <div class="text-gray-600">{{ $ticket['phone'] }}</div>
                            </div>
                            <div class="text-sm text-gray-500">
                                <span class="font-medium">Booking Date:</span>
                                <div class="text-gray-600">{{ date('Y-m-d', strtotime($ticket['booking_time'])) }}</div>
                            </div>
                            <div class="text-sm text-gray-500 font-medium">
                                <span class="font-medium">Total:</span>
                                <div class="text-gray-700 font-bold">{{ $ticket['subtotal'] }}£E</div>
                            </div>
                        </div>
                        <div class="flex justify-between items-center">
                            <a href="{{ route('attractions.show', $ticket['slug']) }}" class="text-primary hover:underline flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                View Attraction
                            </a>
                            <a href="{{ route('tickets.show', $ticket['ticket_id']) }}" class="btn-primary flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                                </svg>
                                View Ticket
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
            
            <div class="flex justify-between mt-4">
                <a href="{{ route('attractions.index') }}" class="btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L9.414 11H13a1 1 0 100-2H9.414l1.293-1.293z" clip-rule="evenodd" />
                    </svg>
                    Browse More Attractions
                </a>
                <a href="{{ route('cart.index') }}" class="text-primary hover:text-primary-dark flex items-center">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                    Go to Cart
                </a>
            </div>
        </div>
    </div>
@else
    <div class="card p-8 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
        </svg>
        <h2 class="text-2xl font-bold text-gray-600 mb-2">You Have No Tickets</h2>
        <p class="text-gray-500 mb-6">Start your Egyptian adventure by booking attractions and experiences.</p>
        <a href="{{ route('attractions.index') }}" class="btn-primary">Browse Attractions</a>
    </div>
@endif

<div class="mt-12">
    <h2 class="text-2xl font-bold text-gray-600 mb-6">Recommended for Your Trip</h2>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        @php
            $attractionController = new App\Http\Controllers\AttractionController();
            $allAttractions = $attractionController->getAttractions();
            $recommended = array_slice($allAttractions, 0, 3);
        @endphp
        
        @foreach($recommended as $attraction)
            <div class="card">
                <div class="relative">
                    <img src="{{ $attraction['image'] }}" alt="{{ $attraction['title'] }}" class="w-full h-48 object-cover">
                    <div class="absolute top-4 left-4 bg-white/80 backdrop-blur-sm px-3 py-1 rounded-full">
                        <p class="text-gray-600 font-medium">
                            From {{ $attraction['price'] }}£E<span class="text-sm">/person</span>
                        </p>
                    </div>
                </div>
                <div class="p-4">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-xl font-bold text-gray-600">{{ $attraction['title'] }}</h3>
                        <div class="flex items-center gap-1">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="text-sm text-gray-600">{{ $attraction['rating'] }}</span>
                        </div>
                    </div>
                    <p class="text-gray-600 mb-4 line-clamp-2">{{ $attraction['description'] }}</p>
                    <a href="{{ route('attractions.show', $attraction['slug']) }}" class="btn-primary w-full">View Details</a>
                </div>
            </div>
        @endforeach
    </div>
</div>
@endsection
