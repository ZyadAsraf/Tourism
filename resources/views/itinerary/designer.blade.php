@extends('layouts.app')

@section('title', 'Itinerary Designer - Aswan 2026')

@push('styles')
<meta name="csrf-token" content="{{ csrf_token() }}">
<style>
    .day-tab {
        transition: all 0.3s ease;
    }
    
    .day-tab:hover {
        transform: translateY(-2px);
    }
    
    #add-attraction-modal {
        transition: all 0.3s ease;
    }
    
    .btn-sm {
        transition: all 0.2s ease;
    }
    
    .btn-sm:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
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
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-600 mb-2">Itinerary Designer</h1>
    <p class="text-gray-500">Create your perfect adventure</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    <!-- Main Itinerary Content -->
    <div class="lg:col-span-2">
        <!-- Day Selection Tabs -->
        <div class="mb-6">
            <div class="flex overflow-x-auto space-x-2 pb-2" id="tabs-container">
                @php
                    // Use the actual days from the itinerary data
                    $days = $itineraryDays ?? [];
                    $maxDay = count($days) > 0 ? max(array_keys($days)) : 0;
                @endphp
                
                @if(count($days) > 0)
                    @foreach($days as $dayNumber => $dayInfo)
                        <button 
                            id="day-tab-{{ $dayNumber }}" 
                            class="day-tab whitespace-nowrap px-4 py-2 rounded-md font-medium text-sm {{ $dayNumber == 1 ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200' }}"
                            onclick="selectDay({{ $dayNumber }})">
                            <div class="text-xs">{{ $dayInfo['day_of_week'] }}</div>
                            <div>Day {{ $dayNumber }}</div>
                            <div class="text-xs">{{ $dayInfo['formatted_date'] }}</div>
                        </button>
                    @endforeach
                @else
                    <button 
                        id="day-tab-1" 
                        class="day-tab whitespace-nowrap px-4 py-2 rounded-md font-medium text-sm bg-primary text-white"
                        onclick="selectDay(1)">
                        Day 1
                    </button>
                @endif
                
                <button class="whitespace-nowrap px-4 py-2 rounded-md font-medium text-sm bg-gray-100 text-gray-600 hover:bg-gray-200" onclick="addNewDay()">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 inline" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd" d="M10 5a1 1 0 011 1v3h3a1 1 0 110 2h-3v3a1 1 0 11-2 0v-3H6a1 1 0 110-2h3V6a1 1 0 011-1z" clip-rule="evenodd" />
                    </svg>
                    Add Day
                </button>
            </div>
        </div>

        <!-- Itinerary Items for Selected Day -->
        @if(count($days) > 0)
            @foreach($days as $dayNumber => $dayInfo)
                <div id="day-content-{{ $dayNumber }}" class="day-content" style="display: {{ $dayNumber == 1 ? 'block' : 'none' }}">
                    <div class="mb-6 flex justify-between items-center">
                        <h2 class="text-xl font-bold text-gray-600">Day {{ $dayNumber }} - {{ $dayInfo['formatted_date'] }} - {{ $itinerary->name ?? 'My Trip' }}</h2>
                        <div class="flex items-center gap-2">
                            
                            <a href="{{ route('attractions.index') }}" class="btn-sm bg-primary text-white rounded-md p-2 text-center inline-block">
                                Add Attraction
                            </a>
                        </div>
                    </div>
                    
                    <div id="attractions-container-{{ $dayNumber }}" class="space-y-4">
                        @if(isset($itineraryItems[$dayNumber]) && count($itineraryItems[$dayNumber]) > 0)
                            @foreach($itineraryItems[$dayNumber] as $index => $attraction)
                                <div class="card p-4 mb-4 flex flex-col md:flex-row gap-4" id="attraction-{{ $attraction['uuid'] }}">
                                    <div class="w-full md:w-1/4">
                                        <img src="{{ $attraction['image'] }}" alt="{{ $attraction['title'] }}" class="w-full h-32 object-cover rounded-lg">
                                    </div>
                                    <div class="w-full md:w-3/4">
                                        <div class="flex justify-between items-start mb-2">
                                            <h3 class="text-xl font-bold text-gray-600">{{ $attraction['title'] }}</h3>
                                            <button onclick="removeAttraction('{{ $attraction['uuid'] }}')" class="text-red-500 hover:text-red-700">
                                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>
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
                                <p class="text-gray-500 mb-4">Start building your itinerary by adding attractions for this day.</p>
                                <button class="btn-primary" onclick="showAddAttractionModal({{ $dayNumber }})">Add Attraction</button>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach
        @else
            <!-- Default Day 1 content when no itinerary days exist -->
            <div id="day-content-1" class="day-content" style="display: block">
                <div class="mb-6 flex justify-between items-center">
                    <h2 class="text-xl font-bold text-gray-600">Day 1 - {{ $itinerary->name ?? 'My Trip' }}</h2>
                    <div class="flex items-center gap-2">
                        <a href="{{ route('attractions.index') }}" class="btn-sm bg-gray-600 text-white rounded-md p-2 flex items-center" title="Browse Attractions">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                                <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                            </svg>
                        </a>
                        <button class="btn-sm bg-primary text-white rounded-md p-2" onclick="showAddAttractionModal(1)">
                            Add Attraction
                        </button>
                    </div>
                </div>
                
                <div id="attractions-container-1" class="space-y-4">
                    <div class="text-center p-6 bg-gray-50 rounded-lg">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-12 w-12 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6" />
                        </svg>
                        <h3 class="text-lg font-medium text-gray-600 mb-2">No Attractions Added for Day 1</h3>
                        <p class="text-gray-500 mb-4">Start building your itinerary by adding attractions for this day.</p>
                        <button class="btn-primary" onclick="showAddAttractionModal(1)">Add Attraction</button>
                    </div>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Itinerary Summary Sidebar -->
    <div class="lg:col-span-1">
        <div class="card p-6 sticky top-4">
            <h2 class="text-xl font-bold mb-4 text-gray-600">Itinerary Summary</h2>
            
            <div class="mb-6">
                <form action="{{ route('itinerary.update') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-gray-600 text-sm mb-1">Name</label>
                        <input type="text" name="name" value="{{ $itinerary->name }}" class="w-full p-2 border border-gray-200 rounded-md text-sm">
                    </div>
                    
                    <div>
                        <label class="block text-gray-600 text-sm mb-1">Type</label>
                        <select name="type_id" class="w-full p-2 border border-gray-200 rounded-md text-sm">
                            @foreach($itineraryTypes as $type)
                                <option value="{{ $type->id }}" {{ $itinerary->type_id == $type->id ? 'selected' : '' }}>{{ $type->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-gray-600 text-sm mb-1">Description</label>
                        <textarea name="description" class="w-full p-2 border border-gray-200 rounded-md text-sm" rows="3">{{ $itinerary->description }}</textarea>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" name="public" id="public" value="1" {{ $itinerary->public ? 'checked' : '' }} class="mr-2">
                        <label for="public" class="text-gray-600 text-sm">Make public</label>
                    </div>
                    
                    <button type="submit" class="btn-primary w-full">Save Itinerary</button>
                </form>
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
            
            <button id="add-all-to-cart-btn" class="btn-primary w-full block text-center">Add All to Cart</button>
        </div>
    </div>
</div>

<!-- Add Attraction Modal -->
<div id="add-attraction-modal" class="fixed inset-0 bg-gray-800 bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white rounded-lg p-6 w-full max-w-2xl max-h-[90vh] overflow-y-auto">
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-xl font-bold text-gray-600">Add Attraction</h2>
            <button onclick="closeAddAttractionModal()" class="text-gray-500 hover:text-gray-700">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        
        <form action="{{ route('itinerary.add-attraction') }}" method="POST" id="add-attraction-form">
            @csrf
            <input type="hidden" name="day" id="selected-day" value="1">
            <input type="hidden" name="itinerary_uuid" value="{{ $itinerary->uuid }}">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-gray-600 mb-1">Attraction</label>
                    <select name="attraction_id" class="w-full p-2 border border-gray-200 rounded-md" required>
                        <option value="">Select an attraction</option>
                        @foreach($attractions as $slug => $attraction)
                            <option value="{{ $attraction['id'] }}">{{ $attraction['title'] }} ({{ $attraction['price'] }}£E)</option>
                        @endforeach
                    </select>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-600 mb-1">Date</label>
                        <input type="date" name="date" class="w-full p-2 border border-gray-200 rounded-md" required value="{{ date('Y-m-d') }}">
                    </div>
                    <div>
                        <label class="block text-gray-600 mb-1">Time</label>
                        <select name="time" class="w-full p-2 border border-gray-200 rounded-md">
                            <option value="morning">Morning (9:00 AM)</option>
                            <option value="afternoon">Afternoon (1:00 PM)</option>
                            <option value="evening">Evening (5:00 PM)</option>
                        </select>
                    </div>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-gray-600 mb-1">Guests</label>
                        <select name="quantity" class="w-full p-2 border border-gray-200 rounded-md">
                            @for($i = 1; $i <= 10; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                            @endfor
                        </select>
                    </div>
                    <div>
                        <label class="block text-gray-600 mb-1">Ticket Type</label>
                        <select name="ticket_type_id" class="w-full p-2 border border-gray-200 rounded-md">
                            @foreach(App\Models\TicketType::all() as $ticketType)
                                <option value="{{ $ticketType->id }}">{{ $ticketType->Title }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                
                <div class="flex justify-end space-x-2">
                    <button type="button" onclick="closeAddAttractionModal()" class="px-4 py-2 border border-gray-300 rounded-md text-gray-600 hover:bg-gray-50">
                        Cancel
                    </button>
                    <button type="submit" class="btn-primary">
                        Add to Itinerary
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Form for removing attractions -->
<form id="remove-attraction-form" action="{{ route('itinerary.remove-attraction', 'ID_TO_REPLACE') }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>

@push('scripts')
<script type="text/javascript">
    let currentDay = 1;
    
    // Function to select a day tab
    function selectDay(day) {
        currentDay = day;
        
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
        
        // Update the selected day in the Add Attraction form
        document.getElementById('selected-day').value = day;
    }

    // Function to show the Add Attraction modal
    function showAddAttractionModal(day) {
        currentDay = day;
        document.getElementById('selected-day').value = day;
        document.getElementById('add-attraction-modal').style.display = 'flex';
    }

    // Function to close the Add Attraction modal
    function closeAddAttractionModal() {
        document.getElementById('add-attraction-modal').style.display = 'none';
    }

    // Function to remove an attraction
    function removeAttraction(id) {
        if (confirm('Are you sure you want to remove this attraction from your itinerary?')) {
            const form = document.getElementById('remove-attraction-form');
            form.action = form.action.replace('ID_TO_REPLACE', id);
            form.submit();
        }
    }

    // Function to add a new day
    function addNewDay() {
        // Logic will be handled by the controller
        // This is just a placeholder function for the button
    }

    // Add all itinerary items to cart
    // Initialize variables for JavaScript with PHP data
    document.addEventListener('DOMContentLoaded', function() {
        const addAllToCartBtn = document.getElementById('add-all-to-cart-btn');

        if (addAllToCartBtn) {
            addAllToCartBtn.addEventListener('click', function() {
                // Disable button to prevent multiple clicks
                addAllToCartBtn.disabled = true;
                addAllToCartBtn.textContent = 'Adding...';
                
                // Get itinerary UUID for direct controller method
                const itineraryUuid = "{{ $itinerary->uuid }}";
                
                // Create a form to submit
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = "{{ route('cart.add-itinerary') }}";
                
                // Add CSRF token
                const csrfToken = document.createElement('input');
                csrfToken.type = 'hidden';
                csrfToken.name = '_token';
                csrfToken.value = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
                form.appendChild(csrfToken);
                
                // Add itinerary UUID
                const itineraryInput = document.createElement('input');
                itineraryInput.type = 'hidden';
                itineraryInput.name = 'itinerary_uuid';
                itineraryInput.value = itineraryUuid;
                form.appendChild(itineraryInput);
                
                // Add to DOM and submit
                document.body.appendChild(form);
                form.submit();
            });
        }
    });
</script>
@endpush

@endsection
