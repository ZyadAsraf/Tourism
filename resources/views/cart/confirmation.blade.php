@extends('layouts.app')

@section('title', 'Booking Confirmation - Massar')

@section('content')
<div class="text-center mb-12">
    <div class="inline-flex items-center justify-center w-24 h-24 bg-green-100 rounded-full mb-6">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
    </div>
    <h1 class="text-3xl font-bold text-gray-600 mb-2">Booking Confirmed!</h1>
    <p class="text-gray-500 max-w-2xl mx-auto">Thank you for booking your Egyptian adventure with Massar. Your trip has been confirmed and details have been sent to your email.</p>
</div>

<div class="card p-8 max-w-3xl mx-auto mb-12">
    <h2 class="text-2xl font-bold text-gray-600 mb-6">Booking Details</h2>
    
    <div class="space-y-6">
        <div>
            <h3 class="font-bold text-gray-600 mb-2">Booking Reference</h3>
            <p class="text-gray-600">MASSAR-{{ strtoupper(substr(md5(time()), 0, 8)) }}</p>
        </div>
        
        <div>
            <h3 class="font-bold text-gray-600 mb-2">Payment Status</h3>
            <div class="inline-flex items-center px-3 py-1 rounded-full bg-green-100 text-green-800">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
                <span>Confirmed</span>
            </div>
        </div>
        
        <div>
            <h3 class="font-bold text-gray-600 mb-2">What's Next?</h3>
            <ul class="space-y-2 text-gray-600">
                <li class="flex items-start gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                    </svg>
                    <span>Check your email for a detailed confirmation of your booking.</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                    <span>Save the booking details and present them at each attraction.</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span>Arrive at least 15 minutes before your scheduled time.</span>
                </li>
                <li class="flex items-start gap-2">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z" />
                    </svg>
                    <span>If you have any questions, contact our support team at support@massar.com.</span>
                </li>
            </ul>
        </div>
    </div>
</div>

<div class="text-center mb-12">
    <a href="{{ route('home') }}" class="btn-primary">Return to Home</a>
</div>

<div class="mb-12">
    <h2 class="text-2xl font-bold text-gray-600 mb-6 text-center">More to Explore</h2>
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
