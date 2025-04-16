@extends('layouts.app')

@section('title', $categoryName . ' - TravelEgypt')

@section('content')
  <div class="mb-8">
      <h1 class="text-3xl font-bold text-gray-600 mb-2">{{ $categoryName }}</h1>
      <p class="text-gray-500">Discover the best {{ strtolower($categoryName) }} Egypt has to offer</p>
  </div>
  
  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
      @foreach($attractions as $attraction)
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
                  <p class="text-gray-600 mb-4 line-clamp-2">{{ $attraction['description'] }}</p>
                  <div class="flex justify-between items-center">
                      <span class="text-sm text-gray-500">{{ $attraction['location'] }} • {{ $attraction['duration'] }}</span>
                      <a href="{{ route('attractions.show', $attraction['slug']) }}" class="btn-primary">View Details</a>
                  </div>
              </div>
          </div>
      @endforeach
  </div>
@endsection

