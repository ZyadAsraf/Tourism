@extends('layouts.app')

@section('title', 'All Attractions - TravelEgypt')

@section('content')
  <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-600 mb-2">All Attractions</h1>
      <p class="text-gray-500">Discover the best attractions and experiences Egypt has to offer</p>
  </div>
  
  <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
      <!-- Filters Sidebar -->
      <div class="lg:col-span-1">
          <div class="card p-4 sticky top-4">
              <h2 class="text-xl font-bold mb-4 text-gray-600">Filters</h2>
              
              <div class="mb-6">
                  <h3 class="font-medium mb-2 text-gray-600">Categories</h3>
                  <div class="space-y-2">
                      @foreach($categories as $slug => $name)
                          <div class="flex items-center">
                              <input type="checkbox" id="category-{{ $slug }}" class="mr-2">
                              <label for="category-{{ $slug }}" class="text-gray-600">{{ $name }}</label>
                          </div>
                      @endforeach
                  </div>
              </div>
              
              <div class="mb-6">
                  <h3 class="font-medium mb-2 text-gray-600">Price Range</h3>
                  <div class="flex items-center">
                      <input type="number" placeholder="Min" class="w-full p-2 border border-gray-200 rounded-md mr-2">
                      <span class="text-gray-400">-</span>
                      <input type="number" placeholder="Max" class="w-full p-2 border border-gray-200 rounded-md ml-2">
                  </div>
              </div>
              
              <div class="mb-6">
                  <h3 class="font-medium mb-2 text-gray-600">Duration</h3>
                  <div class="space-y-2">
                      <div class="flex items-center">
                          <input type="checkbox" id="duration-1" class="mr-2">
                          <label for="duration-1" class="text-gray-600">Less than 3 hours</label>
                      </div>
                      <div class="flex items-center">
                          <input type="checkbox" id="duration-2" class="mr-2">
                          <label for="duration-2" class="text-gray-600">3-5 hours</label>
                      </div>
                      <div class="flex items-center">
                          <input type="checkbox" id="duration-3" class="mr-2">
                          <label for="duration-3" class="text-gray-600">Full day</label>
                      </div>
                      <div class="flex items-center">
                          <input type="checkbox" id="duration-4" class="mr-2">
                          <label for="duration-4" class="text-gray-600">Multi-day</label>
                      </div>
                  </div>
              </div>
              
              <button class="btn-primary w-full">Apply Filters</button>
          </div>
      </div>
      
      <!-- Attractions Grid -->
      <div class="lg:col-span-3">
          <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
              @forelse($attractions as $attraction)
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
                                  <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 star-rating" viewBox="0 0 20 20" fill="currentColor">
                                      <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                  </svg>
                                  <span class="text-sm text-gray-600">{{ $attraction['rating'] }} ({{ number_format($attraction['reviewCount']) }})</span>
                              </div>
                          </div>
                          <p class="text-gray-600 mb-4 line-clamp-2">{!! $attraction['description'] !!}</p>
                          <div class="flex justify-between items-center">
                              <span class="text-sm text-gray-500">{{ $attraction['location'] }} • {{ $attraction['duration'] }}</span>
                              <a href="{{ route('attractions.show', $attraction['slug']) }}" class="btn-primary">View Details</a>
                          </div>
                      </div>
                  </div>
              @empty
                  <div class="col-span-3 text-center py-12">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                      </svg>
                      <h2 class="text-2xl font-bold text-gray-600 mb-2">No attractions found</h2>
                      <p class="text-gray-500 mb-6">There are currently no attractions available. Please check back later.</p>
                  </div>
              @endforelse
          </div>
      </div>
  </div>
@endsection

