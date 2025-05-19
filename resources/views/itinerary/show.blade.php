@extends('layouts.app')

@section('title', $itinerary->name . ' Itinerary - Aswan 2026')

@push('styles')
<style>
    .day-tab {
        transition: all 0.3s ease;
    }
    
    .day-tab:hover {
        transform: translateY(-2px);
    }
    
    #tabs-container {
        scrollbar-width: thin;
        scrollbar-color: var(--color-primary) #f0f0f0;
    }
    
    #tabs-container::-webkit-scrollbar {
        height: 6px;
    }
    
    #tabs-container::-webkit-scrollbar-track {
        background: #f0f0f0;
    }
    
    #tabs-container::-webkit-scrollbar-thumb {
        background-color: var(--color-primary);
        border-radius: 6px;
    }
</style>
@endpush

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Itinerary Content -->
    <div class="lg:col-span-2">
        <!-- Day Selection Tabs -->
        <div class="mb-6">
            <div class="flex overflow-x-auto space-x-2 pb-2" id="tabs-container">
                @foreach($itineraryDays as $dayNumber => $dayInfo)
                    <button 
                        id="day-tab-{{ $dayNumber }}" 
                        class="day-tab whitespace-nowrap px-4 py-2 rounded-md font-medium text-sm {{ $dayNumber == 1 ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                        onclick="selectDay({{ $dayNumber }})">
                        <div class="text-xs">{{ $dayInfo['day_of_week'] }}</div>
                        <div>Day {{ $dayNumber }}</div>
                        <div class="text-xs">{{ $dayInfo['formatted_date'] }}</div>
                    </button>
                @endforeach
            </div>
        </div>

        <!-- Itinerary Items for Each Day -->
        @foreach($itineraryItems as $dayNumber => $items)
            <div id="day-content-{{ $dayNumber }}" class="day-content" style="display: {{ $dayNumber == 1 ? 'block' : 'none' }}">
                <div class="mb-6">
                    <h2 class="text-xl font-bold text-gray-600">Day {{ $dayNumber }} - {{ $itineraryDays[$dayNumber]['formatted_date'] }} - {{ $itinerary->name }}</h2>
                </div>
                
                <div class="space-y-4">
                    @if(count($items) > 0)
                        @foreach($items as $index => $attraction)
                            <div class="card p-4 mb-4 flex flex-col md:flex-row gap-4">
                                <div class="w-full md:w-1/4">
                                    <img src="{{ $attraction['image'] }}" alt="{{ $attraction['title'] }}" class="w-full h-32 object-cover rounded-lg">
                                </div>
                                <div class="w-full md:w-3/4">
                                    <h3 class="text-xl font-bold text-gray-600 mb-2">{{ $attraction['title'] }}</h3>
                                    <div class="flex items-center gap-1 mb-2">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                        </svg>
                                        <span class="text-sm text-gray-600">{{ $attraction['rating'] }} ({{ number_format($attraction['reviewCount']) }})</span>
                                    </div>
                                    <p class="text-gray-600 mb-4">{{ $attraction['description'] }}</p>
                                    <div class="flex flex-wrap gap-4 items-center">
                                        <div class="text-sm text-gray-500">
                                            <span class="font-medium">Date:</span> {{ date('Y-m-d', strtotime($attraction['date'])) }}
                                        </div>
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
                            <h3 class="text-lg font-medium text-gray-600 mb-2">No Attractions Added for Day {{ $dayNumber }}</h3>
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
            
            <div class="mb-6">
                <p class="text-gray-600 mb-4">{{ $itinerary->description }}</p>
                
                <div class="flex items-center mb-2">
                    <span class="text-gray-500 font-medium mr-2">Created by:</span>
                    <span class="text-gray-600">{{ $itinerary->user->name ?? 'Anonymous' }}</span>
                </div>
                
                <div class="flex items-center mb-4">
                    <span class="text-gray-500 font-medium mr-2">Type:</span>
                    <span class="text-gray-600">{{ $itinerary->type->name ?? 'Custom' }}</span>
                </div>
                
                @if(Auth::check() && $itinerary->user_id != Auth::id())
                    <form action="{{ route('itinerary.copy', $itinerary->uuid) }}" method="POST" class="mb-4">
                        @csrf
                        <button type="submit" class="btn-primary w-full">Copy to My Itineraries</button>
                    </form>
                @endif
                
                @if(Auth::check() && $itinerary->user_id == Auth::id())
                    <a href="{{ route('itinerary.designer', $itinerary->uuid) }}" class="btn-primary w-full block text-center mb-4">Edit Itinerary</a>
                @endif
            </div>
            
            <div class="space-y-4 mb-6">
                <div class="flex justify-between">
                    <span class="text-gray-600">Number of Attractions:</span>
                    <span class="text-gray-600">{{ $stats['totalAttractions'] }}</span>
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
            
            <a href="#" class="btn-primary w-full block text-center">Export Itinerary</a>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Function to select a day tab
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
