@extends('layouts.app')

@section('title', 'Cart - Massar')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-600 mb-2">My Cart</h1>
    <p class="text-gray-500">Review your cart before purchase</p>
</div>

@if(count($attractions) > 0)
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <div class="lg:col-span-2">
            @foreach($attractions as $attraction)
                <div class="card p-4 mb-4 flex flex-col md:flex-row gap-4">
                    <div class="w-full md:w-1/4">
                        <img src="{{ $attraction['image'] }}" alt="{{ $attraction['title'] }}" class="w-full h-32 object-cover rounded-lg">
                    </div>
                    <div class="w-full md:w-3/4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold text-gray-600">{{ $attraction['title'] }}</h3>
                            <a href="{{ route('cart.remove', $attraction['slug']) }}" class="text-red-500 hover:text-red-700" onclick="return confirm('Are you sure you want to remove this attraction from your cart?')">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </a>
                        </div>
                        <div class="flex items-center gap-1 mb-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                            </svg>
                            <span class="text-sm text-gray-600">{{ $attraction['rating'] }} ({{ number_format($attraction['reviewCount']) }})</span>
                        </div>
                        <p class="text-gray-600 mb-4 line-clamp-2">{{ $attraction['description'] }}</p>
                        <div class="flex flex-wrap gap-4 items-center">
                            <div class="text-sm text-gray-500">
                                <span class="font-medium">Date:</span> {{ $attraction['date'] }}
                            </div>
                            <div class="text-sm text-gray-500">
                                <span class="font-medium">Time:</span> {{ $attraction['time'] }}
                            </div>
                            <div class="text-sm text-gray-500">
                                <span class="font-medium">Price:</span> {{ $attraction['price'] }}£E/person
                            </div>
                            <form action="{{ route('cart.update', $attraction['slug']) }}" method="POST" class="flex items-center gap-2">
                                @csrf
                                <label for="quantity-{{ $attraction['slug'] }}" class="text-sm text-gray-500 font-medium">Guests:</label>
                                <select name="quantity" id="quantity-{{ $attraction['slug'] }}" class="p-1 border border-gray-200 rounded-md text-sm" onchange="this.form.submit()">
                                    @for($i = 1; $i <= 10; $i++)
                                        <option value="{{ $i }}" {{ $attraction['quantity'] == $i ? 'selected' : '' }}>{{ $i }}</option>
                                    @endfor
                                </select>
                            </form>
                            <div class="ml-auto text-lg font-bold text-gray-700">
                                {{ $attraction['subtotal'] }}£E
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
            
            <div class="flex justify-between mt-4">
                <a href="{{ route('attractions.index') }}" class="btn-primary">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 inline-block mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm.707-10.293a1 1 0 00-1.414-1.414l-3 3a1 1 0 000 1.414l3 3a1 1 0 001.414-1.414L9.414 11H13a1 1 0 100-2H9.414l1.293-1.293z" clip-rule="evenodd" />
                    </svg>
                    Continue Shopping
                </a>
                <a href="{{ route('cart.clear') }}" class="text-red-500 hover:text-red-700 flex items-center" onclick="return confirm('Are you sure you want to clear your entire cart?')">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-1" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M9 2a1 1 0 00-.894.553L7.382 4H4a1 1 0 000 2v10a2 2 0 002 2h8a2 2 0 002-2V6a1 1 0 100-2h-3.382l-.724-1.447A1 1 0 0011 2H9zM7 8a1 1 0 012 0v6a1 1 0 11-2 0V8zm5-1a1 1 0 00-1 1v6a1 1 0 102 0V8a1 1 0 00-1-1z" clip-rule="evenodd" />
                    </svg>
                    Clear Cart
                </a>
            </div>
        </div>
        
        <div class="lg:col-span-1">
            <div class="card p-6 sticky top-4">
                <h2 class="text-xl font-bold mb-4 text-gray-600">Cart Summary</h2>
                
                <div class="space-y-4 mb-6">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Number of Attractions:</span>
                        <span class="text-gray-600">{{ count($attractions) }}</span>
                    </div>
                    
                    @foreach($attractions as $attraction)
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-500">{{ $attraction['title'] }} ({{ $attraction['quantity'] }} {{ $attraction['quantity'] > 1 ? 'guests' : 'guest' }})</span>
                            <span class="text-gray-500">{{ $attraction['subtotal'] }}£E</span>
                        </div>
                    @endforeach
                    
                    <div class="border-t border-gray-200 pt-4 flex justify-between font-bold">
                        <span>Total</span>
                        <span>{{ $total }}£E</span>
                    </div>
                </div>
                
                <a href="{{ route('cart.checkout') }}" class="btn-primary w-full block text-center">Proceed to Checkout</a>
                
                <div class="mt-6 text-sm text-gray-500">
                    <p class="mb-2"><strong>Note:</strong>Prices and availability are subject to change.</p>
                    <p>Need help? <a href="#" class="text-primary hover:underline">Contact our support team</a></p>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="card p-8 text-center">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
        </svg>
        <h2 class="text-2xl font-bold text-gray-600 mb-2">Your Cart is Empty</h2>
        <p class="text-gray-500 mb-6">Start planning your Egyptian adventure by adding attractions to your cart.</p>
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
