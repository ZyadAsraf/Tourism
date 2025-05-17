@extends('layouts.app')

@section('title', 'My Itineraries')

@section('content')
<div class="mb-8">
    <h1 class="text-3xl font-bold text-gray-600 mb-2">My Itineraries</h1>
    <p class="text-gray-500">Travel plans made by you</p>
</div>

<div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
    <!-- Filters Sidebar -->
    <div class="lg:col-span-1">
        <form action="{{ route('itineraries.index') }}" method="GET" class="card p-4 sticky top-4">
            <h2 class="text-xl font-bold mb-4 text-gray-600">Filters</h2>
            
            <!-- <div class="mb-6">
                <h3 class="font-medium mb-2 text-gray-600">Search</h3>
                <input type="text" name="query" placeholder="Search itineraries..." 
                       
                       class="w-full p-2 border border-gray-200 rounded-md">
            </div> -->
            
            <div class="mb-6">
                <h3 class="font-medium mb-2 text-gray-600">Itinerary Type</h3>
                <div class="space-y-2">
                    @foreach($itineraryTypes as $type)
                        <div class="flex items-center">
                            <input type="radio" id="type-{{ $type->id }}" name="type" value="{{ $type->id }}" 
                                   class="mr-2" {{ $currentType == $type->id ? 'checked' : '' }}>
                            <label for="type-{{ $type->id }}" class="text-gray-600">{{ $type->name }}</label>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <button type="submit" class="btn-primary w-full">Apply Filters</button>
            
            @if(request()->has('type') || request()->has('query'))
                <a href="{{ route('itineraries.index') }}" class="text-center block mt-4 text-primary hover:underline">Clear Filters</a>
            @endif
        </form>
    </div>
    
    <!-- Itineraries Grid -->
    <div class="lg:col-span-3">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($itineraries as $itinerary)
                <div class="card">
                    <div class="relative bg-gray-100 h-48 flex items-center justify-center">
                        @if(isset($itinerary->groupedItems[1][0]['image']))
                            <img src="{{ $itinerary->groupedItems[1][0]['image'] }}" alt="{{ $itinerary->name }}" class="w-full h-full object-cover opacity-70">
                        @endif
                        <div class="absolute inset-0 bg-gradient-to-b from-transparent to-gray-700 opacity-60"></div>
                        <div class="absolute bottom-4 left-4 right-4 text-white">
                            <p class="font-bold">{{ $itinerary->name }}</p>
                            <p class="text-sm">{{ $itinerary->type->name }}</p>
                        </div>
                        <div class="absolute top-4 right-4 bg-white/80 backdrop-blur-sm px-3 py-1 rounded-full">
                            <p class="text-gray-600 font-medium text-sm">
                                {{ count($itinerary->groupedItems) }} days
                            </p>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-xl font-bold text-gray-600">{{ $itinerary->name }}</h3>
                        </div>
                        <p class="text-gray-600 mb-4 line-clamp-2">{{ $itinerary->description }}</p>
                        <div class="flex justify-between items-center">
                            <div class="space-y-1">
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                    {{ $itinerary->stats['durationText'] }}
                                </div>
                                <div class="flex items-center text-sm text-gray-500">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                                    </svg>
                                    {{ $itinerary->stats['totalCost'] }}£E
                                </div>
                            </div>
                            <a href="{{ route('itinerary.designer', $itinerary->uuid) }}" class="btn-primary">View Plan</a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-3 text-center py-12">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                    </svg>
                    <h2 class="text-2xl font-bold text-gray-600 mb-2">No itineraries found</h2>
                    <p class="text-gray-500 mb-6">There are currently no  itineraries available with the selected filters. Please try different filters or check back later.</p>
                    @if(request()->has('type') || request()->has('query'))
                        <a href="{{ route('itineraries.index') }}" class="btn-primary">Clear Filters</a>
                    @endif
                </div>
            @endforelse
        </div>
        
        <div class="mt-6">
            {{ $itineraries->links() }}
        </div>
    </div>
</div>

@auth
    <div class="mt-12">
        <div class="text-center">
            <h2 class="text-2xl font-bold text-gray-600 mb-2">Create Your Own Itinerary</h2>
            <p class="text-gray-500 mb-6">Use our itinerary designer to create and share your perfect Egypt adventure</p>
            <a href="{{ route(name: 'itinerary.newItinerary') }}" class="btn-primary">Create Itinerary</a>
        </div>
    </div>
@endauth

@endsection
