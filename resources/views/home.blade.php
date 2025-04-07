@extends('layouts.app')

@section('title', 'TravelEgypt - Discover Egypt\'s Hidden Gems')

@section('content')
  <!-- Hero Section -->
  <section class="relative rounded-xl overflow-hidden mb-12">
      <img src="/images/egypt-hero.jpg" alt="Egypt Tourism" class="w-full h-[600px] object-cover">
      <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex flex-col justify-end p-8">
          <div class="text-white max-w-3xl">
              <h1 class="text-5xl font-bold mb-4">Discover the Wonders of Egypt</h1>
              <p class="text-xl mb-8">Explore ancient temples, breathtaking landscapes, and unforgettable experiences</p>
              <div class="flex flex-wrap gap-4">
                  <a href="{{ route('attractions.index') }}" class="btn-primary">Browse Attractions</a>
                  @if(isset($categories['historical']))
                    <a href="{{ route('attractions.category', 'historical') }}" class="btn-outline">Historical Sites</a>
                  @endif
                  @if(isset($categories['adventure']))
                    <a href="{{ route('attractions.category', 'adventure') }}" class="btn-outline">Adventure Tours</a>
                  @endif
              </div>
          </div>
      </div>
  </section>

  <!-- Banners Section (if available) -->
  @if(isset($banners) && $banners->count() > 0)
  <section class="mb-12">
      <div class="flex justify-between items-center mb-6">
          <h2 class="text-3xl font-bold text-gray-600">Special Offers</h2>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
          @foreach($banners as $banner)
              <div class="card overflow-hidden">
                  <a href="{{ $banner->click_url }}" target="{{ $banner->click_url_target }}">
                      @if($banner->getFirstMediaUrl('banners'))
                          <img src="{{ $banner->getFirstMediaUrl('banners') }}" alt="{{ $banner->title }}" class="w-full h-48 object-cover">
                      @elseif($banner->image_url)
                          <img src="{{ $banner->image_url }}" alt="{{ $banner->title }}" class="w-full h-48 object-cover">
                      @endif
                      <div class="p-4">
                          <h3 class="text-xl font-bold text-gray-600">{{ $banner->title }}</h3>
                          <p class="text-gray-600 mb-2">{{ $banner->description }}</p>
                      </div>
                  </a>
              </div>
          @endforeach
      </div>
  </section>
  @endif

  <!-- Featured Attractions Section -->
  <section class="mb-12">
      <div class="flex justify-between items-center mb-6">
          <h2 class="text-3xl font-bold text-gray-600">Featured Attractions</h2>
          <a href="{{ route('attractions.index') }}" class="text-primary hover:text-primary-dark font-medium">View all →</a>
      </div>
      
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
          @forelse($featured as $attraction)
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
  </section>

  <!-- Categories Section -->
  <section class="mb-12">
      <h2 class="text-3xl font-bold mb-6 text-gray-600">Explore by Category</h2>
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
          @forelse($categories as $slug => $name)
              <a href="{{ route('attractions.category', $slug) }}" class="card p-6 hover:shadow-md transition-shadow">
                  <h3 class="text-xl font-bold mb-2 text-gray-600">{{ $name }}</h3>
                  <p class="text-gray-600 mb-4">Discover amazing {{ strtolower($name) }} across Egypt</p>
                  <span class="text-primary font-medium">Explore →</span>
              </a>
          @empty
              <div class="col-span-4 text-center py-12">
                  <p class="text-gray-500">No categories available at the moment.</p>
              </div>
          @endforelse
      </div>
  </section>

  <!-- Why Choose Us Section -->
  <section class="mb-12">
      <h2 class="text-3xl font-bold mb-6 text-gray-600">Why Choose TravelEgypt</h2>
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
          <div class="card p-6">
              <div class="flex items-center mb-4">
                  <div class="bg-primary/10 p-3 rounded-full mr-4">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                      </svg>
                  </div>
                  <h3 class="text-xl font-bold text-gray-600">Verified Attractions</h3>
              </div>
              <p class="text-gray-600">All our attractions are carefully selected and verified for quality and authenticity.</p>
          </div>
          <div class="card p-6">
              <div class="flex items-center mb-4">
                  <div class="bg-primary/10 p-3 rounded-full mr-4">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                  </div>
                  <h3 class="text-xl font-bold text-gray-600">Best Prices</h3>
              </div>
              <p class="text-gray-600">We guarantee the best prices for all attractions with no hidden fees or charges.</p>
          </div>
          <div class="card p-6">
              <div class="flex items-center mb-4">
                  <div class="bg-primary/10 p-3 rounded-full mr-4">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                      </svg>
                  </div>
                  <h3 class="text-xl font-bold text-gray-600">24/7 Support</h3>
              </div>
              <p class="text-gray-600">Our customer support team is available 24/7 to assist you with any questions or issues.</p>
          </div>
      </div>
  </section>
@endsection

