@extends('layouts.app')

@section('title', $itinerary->name . ' - Aswan 2026')

@section('content')
<div class="mb-8">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-600 mb-2">{{ $itinerary->name }}</h1>
            <p class="text-gray-500">{{ $itinerary->description }}</p>
            <div class="mt-2 flex items-center text-sm text-gray-500">
                <span class="mr-4">Created by: {{ $itinerary->user->name }}</span>
                <span class="mr-4">Type: {{ $itinerary->type->name }}</span>
                <span>{{ $itinerary->created_at->format('F j, Y') }}</span>
            </div>
        </div>
        @auth
            @if($itinerary->user_id === Auth::id())
                <a href="{{ route('itinerary.designer', $itinerary->uuid) }}" class="btn-primary">Edit Itinerary</a>
            @endif
        @endauth
    </div>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Itinerary Content -->
    <div class="lg:col-span-2">
        <!-- Day Selection Tabs -->
        <div class="mb-6">
            <div class="flex overflow-x-auto space-x-2 pb-2" id="tabs-container">
                @foreach($itineraryItems as $day => $items)
                    <button 
                        id="day-tab-{{ $day }}" 
                        class="day-tab whitespace-nowrap px-4 py-2 rounded-md font-medium text-sm {{ $day == 1 ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                        onclick="selectDay({{ $day }})">
                        Day {{ $day }}
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Itinerary Items for Each Day -->
        @foreach($itineraryItems as $day => $items)
            <div id="day-content-{{ $day }}" class="day-content" style="display: {{ $day == 1 ? 'block' : 'none' }}">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-600">Day {{ $day }} - {{ $itinerary->name }}</h2>
                </div>
                
                <div class="space-y-4">
                    @if(count($items) > 0)
                        @foreach($items as $index => $attraction)
                            <div class="card p-4 mb-4 flex flex-col md:flex-row gap-4">
                                <div class="w-full md:w-1/4">
                                    <img src="{{ $attraction['image'] }}" alt="{{ $attraction['title'] }}" class="w-full h-32 object-cover rounded-lg">
                                </div>
                                <div class="w-full md:w-3/4">
                                    <div class="flex justify-between items-start mb-2">
                                        <h3 class="text-xl font-bold text-gray-600">{{ $attraction['title'] }}</h3>
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
                                            <span class="font-medium">Time:</span> {{ $attraction['time'] ?? 'Morning' }}
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <span class="font-medium">Price:</span> {{ $attraction['price'] }}£E/person
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <span class="font-medium">Guests:</span> {{ $attraction['quantity'] }}
                                        </div>
                                        <div class="ml-auto text-lg font-bold text-gray-700">
                                            {{ $attraction['subtotal'] }}£E
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @else
                        <div class="text-center p-6 bg-gray-50 rounded-lg">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                            </svg>
                            <h3 class="text-lg font-medium text-gray-600 mb-2">No Attractions Added for Day {{ $day }}</h3>
                            <p class="text-gray-500 mb-4">This day has no planned activities.</p>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach
    </div>
    
    <!-- Itinerary Summary Sidebar -->
    <div class="lg:col-span-1">
        <div class="card p-6 sticky top-4">
            <h2 class="text-xl font-bold mb-4 text-gray-600">Itinerary Summary</h2>
            
            <div class="space-y-4 mb-6">
                <div class="flex justify-between">
                    <span class="text-gray-600">Number of Attractions:</span>
                    <span class="text-gray-600">{{ $stats['totalAttractions'] }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Number of Days:</span>
                    <span class="text-gray-600">{{ count($itineraryItems) }}</span>
                </div>
                
                <div class="flex justify-between">
                    <span class="text-gray-600">Expected Duration:</span>
                    <span class="text-gray-600">{{ $stats['durationText'] }}</span>
                </div>
                
                <div class="border-t border-gray-200 pt-4 flex justify-between font-bold">
                    <span>Total Cost</span>
                    <span>{{ $stats['totalCost'] }}£E</span>
                </div>
            </div>
            
            @auth
                <div class="text-center">
                    <p class="text-sm text-gray-500 mb-4">Like this itinerary? Create your own personalized version!</p>
                    <a href="{{ route('itinerary.copy', ['uuid' => $itinerary->uuid]) }}" class="btn-primary w-full block">Create My Own</a>
                </div>
            @else
                <div class="text-center">
                    <p class="text-sm text-gray-500 mb-4">Sign in to create your own itineraries</p>
                    <a href="{{ route('login') }}" class="btn-primary w-full block">Sign In</a>
                </div>
            @endauth
        </div>
    </div>
</div>

@push('scripts')
<script>
    function selectDay(day) {
        // Hide all day content
        document.querySelectorAll('.day-content').forEach(function(content) {
            content.style.display = 'none';
        });
        
        // Show selected day content
        const selectedDayContent = document.getElementById('day-content-' + day);
        if (selectedDayContent) {
            selectedDayContent.style.display = 'block';
        }
        
        // Update active tab styling
        document.querySelectorAll('.day-tab').forEach(function(tab) {
            tab.classList.remove('bg-primary', 'text-white');
            tab.classList.add('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
        });
        
        const activeTab = document.getElementById('day-tab-' + day);
        if (activeTab) {
            activeTab.classList.remove('bg-gray-100', 'text-gray-600', 'hover:bg-gray-200');
            activeTab.classList.add('bg-primary', 'text-white');
        }
    }
</script>
@endpush

@endsection
