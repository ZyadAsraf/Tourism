@extends('layouts.app')

@section('title', 'Massar - Discover Egypt\'s Hidden Gems')

@section('content')

<!-- Hero Section with Video Background -->
<section class="relative h-[80vh] overflow-hidden mb-16">
    <div class="absolute inset-0 bg-black">
        <img src="images/image3.jpg" alt="Egypt Tourism" class="w-full h-full object-cover opacity-80" onerror="this.src='/images/placeholder.jpg'; this.onerror=null;">
    </div>
    <div class="absolute inset-0 bg-gradient-to-t from-black/80 via-black/40 to-transparent"></div>
    <div class="container mx-auto px-4 h-full flex items-center relative z-10">
        <div class="max-w-3xl text-white">
            <h1 class="text-5xl md:text-6xl font-bold mb-4 leading-tight">Experience the Magic of <span class="text-primary">Egypt</span></h1>
            <p class="text-xl md:text-2xl mb-8 text-gray-100">Discover ancient wonders, breathtaking landscapes, and unforgettable experiences in the land of the pharaohs</p>
            <div class="flex flex-wrap gap-4">
                <a href="{{ route('attractions.index') }}" class="btn-primary text-lg px-8 py-3 rounded-full">Explore Attractions</a>
                @if(isset($categories['historical']))
                    <a href="{{ route('attractions.category', 'historical') }}" class="btn-outline text-lg px-8 py-3 rounded-full">Historical Sites</a>
                @endif
            </div>
        </div>
    </div>
    
    <!-- Scroll Down Indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white text-center">
        <p class="mb-2 text-sm uppercase tracking-widest">Scroll Down</p>
        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 mx-auto animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3" />
        </svg>
    </div>
</section>

<!-- Featured Attractions Section -->
<section class="container mx-auto px-4 mb-16">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-700">Featured Attractions</h2>
            <p class="text-gray-500 mt-2">Handpicked experiences you shouldn't miss</p>
        </div>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @forelse($featured as $attraction)
            <div class="card group hover:shadow-xl transition-all duration-300">
                <div class="relative overflow-hidden">
                    <img src="{{ $attraction['image'] }}" alt="{{ $attraction['title'] }}" class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-110">
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

<!-- Latest Articles Section -->
<section class="container mx-auto px-4 mb-16">
    <div class="flex justify-between items-center mb-8">
        <div>
            <h2 class="text-3xl font-bold text-gray-700">Latest Articles</h2>
            <p class="text-gray-500 mt-2">Stay updated with our latest travel articles and tips</p>
        </div>
    </div>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @forelse($articles as $article)
            <div class="card group hover:shadow-xl transition-all duration-300">
                <div class="relative overflow-hidden">
                    <img src="{{ $article->Img ? '/storage/' . $article->Img : '/images/placeholder.jpg' }}" alt="{{ $article->ArticleHeading }}" class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-110">
                </div>
                <div class="p-4">
                    <h3 class="text-xl font-bold text-gray-600 mb-2">{{ $article->ArticleHeading }}</h3>
                    <p class="text-gray-600 mb-4 line-clamp-2">{{ Str::limit(strip_tags($article->ArticleBody), 100) }}</p>
                    <a href="{{ route('articles.show', $article->id) }}" class="btn-primary">Read More</a>
                </div>
            </div>
        @empty
            <div class="col-span-3 text-center py-12">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-16 w-16 mx-auto text-gray-400 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <h2 class="text-2xl font-bold text-gray-600 mb-2">No articles found</h2>
                <p class="text-gray-500 mb-6">There are currently no articles available. Please check back later.</p>
            </div>
        @endforelse
    </div>
</section>

<!-- Experience Egypt Section -->
<section class="py-16 bg-gray-50 mb-16">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-700 mb-4">Experience Egypt Like Never Before</h2>
            <p class="text-gray-500 max-w-3xl mx-auto">Discover the diverse experiences that make Egypt a unique destination for travelers from around the world</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Experience 1 -->
            <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="h-48 overflow-hidden">
                    <img src="images/image5.jpg" alt="Historical Sites" class="w-full h-full object-cover" onerror="this.src='/images/placeholder.jpg'; this.onerror=null;">
                </div>
                <div class="p-6">
                    <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Historical Tours</h3>
                    <p class="text-gray-600 mb-4">Step back in time and explore ancient temples, tombs, and archaeological wonders.</p>
                    <a href="{{ route('attractions.index') }}" class="text-primary font-medium hover:underline flex items-center">
                        Explore Historical Sites
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
            
            <!-- Experience 2 -->
            <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="h-48 overflow-hidden">
                    <img src="images/image6.jpg" alt="Desert Adventures" class="w-full h-full object-cover" onerror="this.src='/images/placeholder.jpg'; this.onerror=null;">
                </div>
                <div class="p-6">
                    <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 3v4M3 5h4M6 17v4m-2-2h4m5-16l2.286 6.857L21 12l-5.714 2.143L13 21l-2.286-6.857L5 12l5.714-2.143L13 3z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Desert Adventures</h3>
                    <p class="text-gray-600 mb-4">Experience the magic of Egypt's vast deserts, oases, and stunning landscapes.</p>
                    <a href="{{ route('attractions.index') }}" class="text-primary font-medium hover:underline flex items-center">
                        Discover Desert Tours
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
            
            <!-- Experience 3 -->
            <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="h-48 overflow-hidden">
                    <img src="images/image1.jpg" alt="Nile Cruises" class="w-full h-full object-cover" onerror="this.src='/images/placeholder.jpg'; this.onerror=null;">
                </div>
                <div class="p-6">
                    <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Nile Cruises</h3>
                    <p class="text-gray-600 mb-4">Sail the legendary Nile River and witness Egypt's beauty from the water.</p>
                    <a href="{{ route('attractions.index') }}" class="text-primary font-medium hover:underline flex items-center">
                        Explore Nile Cruises
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
            
            <!-- Experience 4 -->
            <div class="bg-white rounded-xl overflow-hidden shadow-md hover:shadow-lg transition-shadow duration-300">
                <div class="h-48 overflow-hidden">
                    <img src="images/image7.jpg" alt="Cultural Experiences" class="w-full h-full object-cover" onerror="this.src='/images/placeholder.jpg'; this.onerror=null;">
                </div>
                <div class="p-6">
                    <div class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center mb-4">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 01-9 9m9-9a9 9 0 00-9-9m9 9H3m9 9a9 9 0 01-9-9m9 9c1.657 0 3-4.03 3-9s-1.343-9-3-9m0 18c-1.657 0-3-4.03-3-9s1.343-9 3-9m-9 9a9 9 0 019-9" />
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-700 mb-2">Cultural Experiences</h3>
                    <p class="text-gray-600 mb-4">Immerse yourself in Egyptian culture, cuisine, and traditional experiences.</p>
                    <a href="{{ route('attractions.index') }}" class="text-primary font-medium hover:underline flex items-center">
                        Discover Cultural Tours
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 ml-1" viewBox="0 0 20 20" fill="currentColor">
                            <path fill-rule="evenodd" d="M10.293 5.293a1 1 0 011.414 0l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414-1.414L12.586 11H5a1 1 0 110-2h7.586l-2.293-2.293a1 1 0 010-1.414z" clip-rule="evenodd" />
                        </svg>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Section -->
<section class="container mx-auto px-4 mb-16">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-700 mb-4">What Our Travelers Say</h2>
        <p class="text-gray-500 max-w-3xl mx-auto">Read authentic reviews from travelers who experienced the magic of Egypt with us</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <!-- Testimonial 1 -->
        <div class="bg-white rounded-xl p-6 shadow-md">
            <div class="flex items-center mb-4">
                <div class="h-12 w-12 rounded-full overflow-hidden mr-4">
                    <img src="images\pexels-lokmansevim-18731384.jpg" alt="Testimonial" class="h-full w-full object-cover" onerror="this.src='/images/placeholder.jpg'; this.onerror=null;">
                </div>
                <div>
                    <h4 class="font-bold text-gray-700">Sarah Johnson</h4>
                    <p class="text-gray-500 text-sm">United States</p>
                </div>
            </div>
            <div class="flex mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            </div>
            <p class="text-gray-600 italic">"Our trip to Egypt was absolutely magical! The pyramids were breathtaking, and our guide was incredibly knowledgeable. Can't wait to come back!"</p>
        </div>
        
        <!-- Testimonial 2 -->
        <div class="bg-white rounded-xl p-6 shadow-md">
            <div class="flex items-center mb-4">
                <div class="h-12 w-12 rounded-full overflow-hidden mr-4">
                    <img src="images\pexels-gabby-k-5384429.jpg" alt="Testimonial" class="h-full w-full object-cover" onerror="this.src='/images/placeholder.jpg'; this.onerror=null;">
                </div>
                <div>
                    <h4 class="font-bold text-gray-700">David Chen</h4>
                    <p class="text-gray-500 text-sm">Canada</p>
                </div>
            </div>
            <div class="flex mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            </div>
            <p class="text-gray-600 italic">"The Nile cruise was the highlight of our trip. Watching the sunset over the river while enjoying Egyptian cuisine was an unforgettable experience."</p>
        </div>
        
        <!-- Testimonial 3 -->
        <div class="bg-white rounded-xl p-6 shadow-md">
            <div class="flex items-center mb-4">
                <div class="h-12 w-12 rounded-full overflow-hidden mr-4">
                    <img src="images\pexels-iamikeee-2709388.jpg" alt="Testimonial" class="h-full w-full object-cover" onerror="this.src='/images/placeholder.jpg'; this.onerror=null;">
                </div>
                <div>
                    <h4 class="font-bold text-gray-700">Emma Wilson</h4>
                    <p class="text-gray-500 text-sm">United Kingdom</p>
                </div>
            </div>
            <div class="flex mb-4">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-yellow-500" viewBox="0 0 20 20" fill="currentColor">
                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                </svg>
            </div>
            <p class="text-gray-600 italic">"The desert safari was incredible! Watching the sunset over the dunes and spending the night under the stars was a once-in-a-lifetime experience."</p>
        </div>
    </div>
</section>

<!-- Why Choose Us Section -->
<section class="container mx-auto px-4 mb-16">
    <div class="text-center mb-12">
        <h2 class="text-3xl font-bold text-gray-700 mb-4">Why Choose Massar</h2>
        <p class="text-gray-500 max-w-3xl mx-auto">We're dedicated to providing you with the best Egyptian travel experience</p>
    </div>
    
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow duration-300">
            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mb-6 mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-700 mb-2 text-center">Verified Attractions</h3>
            <p class="text-gray-600 text-center">All our attractions are carefully selected and verified for quality and authenticity.</p>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow duration-300">
            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mb-6 mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-700 mb-2 text-center">Best Prices</h3>
            <p class="text-gray-600 text-center">We guarantee the best prices for all attractions with no hidden fees or charges.</p>
        </div>
        
        <div class="bg-white rounded-xl p-6 shadow-md hover:shadow-lg transition-shadow duration-300">
            <div class="w-16 h-16 rounded-full bg-primary/10 flex items-center justify-center mb-6 mx-auto">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-8 w-8 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-700 mb-2 text-center">24/7 Support</h3>
            <p class="text-gray-600 text-center">Our customer support team is available 24/7 to assist you with any questions or issues.</p>
        </div>
    </div>
</section>
@endsection
