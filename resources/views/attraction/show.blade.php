@extends('layouts.app')

@section('title', $attraction['title'] . ' - Massar')

@section('content')
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/core/index.min.css" />
    <div class="mb-6">
        <a href="{{ route('attractions.index') }}" class="inline-flex items-center gap-2 text-gray-600 hover:text-primary">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd"
                    d="M9.707 16.707a1 1 0 01-1.414 0l-6-6a1 1 0 010-1.414l6-6a1 1 0 011.414 1.414L5.414 9H17a1 1 0 110 2H5.414l4.293 4.293a1 1 0 010 1.414z"
                    clip-rule="evenodd" />
            </svg>
            <span>Back to attractions</span>
        </a>
    </div>

    <div class="relative rounded-xl overflow-hidden mb-8">
        <img src="{{ $attraction['image'] }}" alt="{{ $attraction['title'] }}" class="w-full h-[500px] object-cover">
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex flex-col justify-end p-8">
            <div class="text-white">
                <p class="text-primary mb-2">{{ $attraction['category'] ?? 'Attraction' }}</p>
                <h1 class="text-4xl font-bold mb-2">{{ $attraction['title'] }}</h1>
                <div class="flex items-center gap-2 mb-4">
                    <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-primary" viewBox="0 0 20 20"
                        fill="currentColor">
                        <path d="M9.049 2.927c.3-.921 1.603  viewBox="0 0 20 20" fill="currentColor">
                                      <path d=" M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0
                            00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755
                            1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8
                            2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0
                            00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                    </svg>
                    <span class="text-white">{{ $attraction['rating'] ?? '0' }}
                        ({{ number_format($attraction['reviewCount'] ?? 0) }} reviews)</span>
                </div>
                <p class="text-lg mb-4">From {{ $attraction['price'] }}£E<span class="text-sm">/person</span></p>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
        <div class="lg:col-span-2">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-bold text-gray-600">About {{ $attraction['title'] }}</h2>
                <button id="ttsButton" class="btn-primary flex items-center gap-2">
                    <i class="fas fa-volume-up"></i>
                    <span>Listen to Description</span>
                </button>
            </div>
            <p id="attractionDescription" class="text-gray-600 mb-6">{!! $attraction['description'] ?? 'No description available.' !!}</p>

            @if (isset($attraction['longDescription']))
                <p class="text-gray-600 mb-6">{!! $attraction['longDescription'] !!}</p>
            @endif

            @if (isset($attraction['gallery']) && count($attraction['gallery']) > 0)
                <div class="mb-8">
                    <h3 class="text-xl font-bold mb-4 text-gray-600">Gallery</h3>
                    <div class="grid grid-cols-3 gap-4">
                        @foreach ($attraction['gallery'] as $image)
                            <img src="{{ $image }}" alt="{{ $attraction['title'] }}"
                                class="rounded-lg h-32 w-full object-cover">
                        @endforeach
                    </div>
                </div>
            @endif

            @if (isset($attraction['included']) && count($attraction['included']) > 0)
                <div class="mb-8">
                    <h3 class="text-xl font-bold mb-4 text-gray-600">What's Included</h3>

                    <ul class="space-y-2">
                        @foreach ($attraction['included'] as $item)
                            <li class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-green-500 mr-2"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if (isset($attraction['notIncluded']) && count($attraction['notIncluded']) > 0)
                <div class="mb-8">
                    <h3 class="text-xl font-bold mb-4 text-gray-600">Not Included</h3>
                    <ul class="space-y-2">
                        @foreach ($attraction['notIncluded'] as $item)
                            <li class="flex items-center">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 text-red-500 mr-2"
                                    viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd"
                                        d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z"
                                        clip-rule="evenodd" />
                                </svg>
                                <span>{{ $item }}</span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        <div class="lg:col-span-1">
            <div class="card p-6 sticky top-4">
                <h2 class="text-xl font-bold mb-4 text-gray-600">Add to Your Trip</h2>
                <form action="{{ route('cart.add', $attraction['slug']) }}" method="POST">
                    @csrf
                    <div class="space-y-4 mb-6">
                        <div>
                            <label class="block text-gray-600 mb-1">Date</label>
                            <input type="date" name="date" class="w-full p-2 border border-gray-200 rounded-md"
                                required>
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-1">Time</label>
                            <select name="time" class="w-full p-2 border border-gray-200 rounded-md" required>
                                <option value="">Select a time</option>
                                <option value="morning">Morning (9:00 AM)</option>
                                <option value="afternoon">Afternoon (1:00 PM)</option>
                                <option value="evening">Evening (5:00 PM)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-600 mb-1">Number of Guests</label>
                            <select name="quantity" class="w-full p-2 border border-gray-200 rounded-md" required>
                                <option value="1">1 Guest</option>
                                <option value="2">2 Guests</option>
                                <option value="3">3 Guests</option>
                                <option value="4">4 Guests</option>
                                <option value="5">5+ Guests</option>
                            </select>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4 mb-4">
                        <div class="flex justify-between mb-2">
                            <span>Price per person</span>
                            <span>{{ $attraction['price'] }}£E</span>
                        </div>
                        <div class="flex justify-between font-bold text-lg">
                            <span>Starting from</span>
                            <span>{{ $attraction['price'] }}£E</span>
                        </div>
                        <p class="text-xs text-gray-500 text-center mt-1">Final price will be calculated based on number of
                            guests</p>
                    </div>

                    <button type="submit" class="btn-primary w-full mb-4">Add to Trip Plan</button>

                    <div class="flex justify-between mt-4">
                        <a href="{{ route('cart.index') }}" class="text-primary hover:underline">View Trip Plan</a>
                        
                    </div>

                    @guest
                        <p class="text-sm text-gray-500 text-center mt-4">You need to <a href="{{ route('login') }}"
                                class="text-primary">sign in</a> to complete your booking</p>
                    @endguest
                </form>
            </div>
        </div>
    </div>

    {{-- Location Map --}}
    {{-- If the Location is set, display the map --}}

    @if (isset($attraction['mapImage']))
        <div class="mb-12">
            <h2 class="text-2xl font-bold mb-4 text-gray-600">Location</h2>
            @php
                $coords = null;
                if (!empty($attraction['mapImage'])) {
                    if (preg_match('/@(-?\d+\.\d+),(-?\d+\.\d+)/', $attraction['mapImage'], $matches)) {
                        $coords = [$matches[1], $matches[2]];
                    }
                }
            @endphp

            {{-- If coordinates are found, display the map --}}

            @if ($coords)
                <style>
                    .mapbox-wrapper {
                        display: flex;
                        justify-content: start;
                        align-items: center;
                        margin-top: 2rem;
                    }

                    .mapbox-card {
                        background: #fff;
                        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
                        border-radius: 10px;
                        overflow: hidden;
                        width: 800px;
                        max-width: 100%;
                        font-family: Arial, sans-serif;
                    }

                    .mapbox-header {
                        padding: 16px;
                        background: rgba(210, 172, 113, 1);
                        color: rgb(255, 255, 255);
                        font-size: 1.25rem;
                        font-weight: bold;
                        border-bottom: 1px solid #eee;
                    }

                    .mapbox-map {
                        height: 350px;
                        width: 100%;
                    }

                    .mapbox-footer {
                        padding: 16px;
                        display: flex;
                        flex-direction: column;
                        gap: 10px;
                        font-size: 0.95rem;
                        color: #555;
                    }

                    .mapbox-button {
                        text-align: center;
                        padding: 10px;
                        background-color: #0b7dda;
                        color: white;
                        text-decoration: none;
                        border-radius: 5px;
                        transition: background 0.3s;
                    }

                    .mapbox-button:hover {
                        background-color: #0b6cbb;
                    }
                </style>

                <!-- Leaflet CSS -->
                <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />

                {{-- Mapbox --}}
                <div class="mapbox-wrapper">
                    <div class="mapbox-card">
                        <div id="mapbox-map" class="mapbox-map"></div>
                        <div class="mapbox-footer">
                            <span>Map showing the location of
                                {{ $attraction['AttractionName'] ?? $attraction['title'] }}.</span>
                            <a class="mapbox-button" href="{{ $attraction['mapImage'] }}" target="_blank">
                                📍 View in Google Maps
                            </a>
                        </div>
                    </div>
                </div>

                {{-- Leaflet JS --}}
                @push('scripts')
                    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
                    <script>
                        document.addEventListener("DOMContentLoaded", function() {
                            const coords = [{{ $coords[0] ?? 'null' }}, {{ $coords[1] ?? 'null' }}];
                            if (coords[0] && coords[1]) {
                                const map = L.map('mapbox-map').setView(coords, 15);
                                L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {}).addTo(map);
                                L.marker(coords).addTo(map)
                                    .bindPopup('📍 {{ $attraction['AttractionName'] ?? $attraction['title'] }}')
                                    .openPopup();
                            } else {
                                console.error("Invalid coordinates.");
                            }
                        });
                    </script>
                @endpush
            @endif
        </div>
    @endif
    {{-- End Of Map Section --}}

    
    {{-- Replace the separate Gallery, 360 Experience and Reviews sections with this tabbed interface --}}
    <div class="mb-12">
        <h2 class="text-2xl font-bold mb-4 text-gray-600">Explore {{ $attraction['title'] }}</h2>
        
        {{-- Tab Navigation --}}
        <div class="border-b border-gray-200 mb-6">
            <ul class="flex flex-wrap -mb-px text-sm font-medium text-center" id="attraction-tabs" role="tablist">
                @if (isset($attraction['gallery']) && count($attraction['gallery']) > 0)
                <li class="mr-2" role="presentation">
                    <button class="tab-btn inline-block p-4 border-b-2 rounded-t-lg active" id="gallery-tab" data-target="gallery-content" type="button" role="tab" aria-selected="true">
                        Gallery
                    </button>
                </li>
                @endif
                
                @if(!empty($attraction['panorama_images']))
                <li class="mr-2" role="presentation">
                    <button class="tab-btn inline-block p-4 border-b-2 rounded-t-lg" id="experience-tab" data-target="experience-content" type="button" role="tab" aria-selected="false">
                        360° Experience
                    </button>
                </li>
                @endif

                <li class="mr-2" role="presentation">
                    <button class="tab-btn inline-block p-4 border-b-2 rounded-t-lg" id="reviews-tab" data-target="reviews-content" type="button" role="tab" aria-selected="false">
                        Reviews ({{ $reviews->count() }})
                    </button>
                </li>
            </ul>
        </div>
        
        {{-- Tab Content --}}
        <div class="tab-content">
            {{-- Gallery Tab Content --}}
            @if (isset($attraction['gallery']) && count($attraction['gallery']) > 0)
            <div id="gallery-content" class="tab-panel active">
                <div class="carousel-container relative shadow-lg rounded-lg overflow-hidden" style="max-height: 500px;">
                    <div class="carousel-slides flex transition-transform duration-500 ease-in-out">
                        @foreach ($attraction['gallery'] as $index => $image)
                            <div class="carousel-slide min-w-full">
                                <img src="{{ $image }}" alt="{{ $attraction['title'] }} - Image {{ $index + 1 }}" 
                                    class="w-full object-contain mx-auto" style="max-height: 500px;">
                            </div>
                        @endforeach
                    </div>

                    <button class="carousel-control prev absolute top-1/2 left-2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 focus:outline-none z-10">
                        &#10094;
                    </button>
                    <button class="carousel-control next absolute top-1/2 right-2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-2 rounded-full hover:bg-opacity-75 focus:outline-none z-10">
                        &#10095;
                    </button>

                    <div class="carousel-indicators absolute bottom-4 left-1/2 transform -translate-x-1/2 flex space-x-2">
                        @foreach ($attraction['gallery'] as $index => $image)
                            <button class="carousel-indicator h-3 w-3 bg-gray-400 rounded-full hover:bg-gray-600 {{ $index == 0 ? 'active bg-gray-800' : '' }}" data-slide-to="{{ $index }}"></button>
                        @endforeach
                    </div>
                </div>
            </div>
            @endif
            
            {{-- 360° Experience Tab Content --}}
            @if(!empty($attraction['panorama_images']))
            <div id="experience-content" class="tab-panel hidden">
                <div id="viewer" style="width: 100%; height: 500px;"></div>
                <script type="importmap">
                {
                    "imports": {
                        "three": "https://cdn.jsdelivr.net/npm/three/build/three.module.js",
                        "@photo-sphere-viewer/core": "https://cdn.jsdelivr.net/npm/@photo-sphere-viewer/core/index.module.js"
                    }
                }
                </script>
            </div>
            @endif
            
            {{-- Reviews Tab Content --}}
            <div id="reviews-content" class="tab-panel hidden">
                @if ($reviews->count())
                <div class="max-w-2xl max-w bg-white p-6 rounded-lg shadow border border-gray-200 space-y-4">
                    @foreach ($reviews as $review)
                        <div>
                            <div class="flex items-start space-x-4 mb-1">
                                <!-- Image -->
                                <img src="{{ $attraction['image'] }}" alt="{{ $attraction['title'] }}"
                                    class="w-16 h-16 rounded-full object-cover border border-gray-300">
                
                                <!-- Name, date, rating -->
                                <div>
                                    <p class="text-lg font-medium text-gray-700">
                                        {{ $review->tourist?->firstname ?? 'Anonymous' }}
                                        {{ $review->tourist?->lastname ?? '' }}
                                    </p>
                
                                    <p class="text-sm text-gray-400">
                                        {{ $review->created_at->diffForHumans() }}
                                    </p>
                
                                    <div class="text-yellow-400 text-lg ">
                                        @for ($i = 1; $i <= 5; $i++)
                                            <span class="{{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}">
                                                &#9733;
                                            </span>
                                        @endfor
                                    </div>
                                </div>
                            </div>
                
                            <!-- Comment -->
                            <p class="text-gray-600 leading-relaxed">{{ $review->comment }}</p>
                
                            @if (!$loop->last)
                                <hr class="my-4 border-gray-200">
                            @endif
                        </div>
                    @endforeach
                </div>
                @else
                    <p class="text-gray-500 mb-6">No reviews yet. Be the first to write one!</p>
                @endif

                {{-- Write Review Button --}}
                <div class="mt-8 text-center">
                    <a href="{{ route('attractions.reviews', ['slug' => $attraction['slug']]) }}"
                        class="inline-block bg-yellow-400 text-white font-semibold px-6 py-2 rounded shadow hover:bg-yellow-500 transition duration-200">
                        Write Your Review
                    </a>
                </div>
            </div>
        </div>
    </div>

    {{-- Tab and Carousel JavaScript --}}
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Tab Functionality
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabPanels = document.querySelectorAll('.tab-panel');
        let viewer = null;
        
        tabButtons.forEach(button => {
            button.addEventListener('click', function() {
                const targetId = this.getAttribute('data-target');
                
                // Update active tab button
                tabButtons.forEach(btn => {
                    btn.classList.remove('active', 'border-primary', 'text-primary');
                    btn.classList.add('border-transparent', 'hover:border-gray-300');
                    btn.setAttribute('aria-selected', 'false');
                });
                
                this.classList.add('active', 'border-primary', 'text-primary');
                this.classList.remove('border-transparent', 'hover:border-gray-300');
                this.setAttribute('aria-selected', 'true');
                
                // Update active tab panel
                tabPanels.forEach(panel => {
                    panel.classList.add('hidden');
                    panel.classList.remove('active');
                });
                
                document.getElementById(targetId).classList.remove('hidden');
                document.getElementById(targetId).classList.add('active');
                
                // Initialize 360 viewer if that tab is selected
                if (targetId === 'experience-content' && !viewer) {
                    initializeViewer();
                }
            });
        });
        
        // Carousel Functionality
        const container = document.querySelector('.carousel-container');
        if (container) {
            const slidesContainer = container.querySelector('.carousel-slides');
            const slides = container.querySelectorAll('.carousel-slide');
            const prevBtn = container.querySelector('.prev');
            const nextBtn = container.querySelector('.next');
            const indicators = container.querySelectorAll('.carousel-indicator');
            
            let currentIndex = 0;
            const slideCount = slides.length;
            
            // Move to specific slide
            function goToSlide(index) {
                if (index < 0) {
                    currentIndex = slideCount - 1;
                } else if (index >= slideCount) {
                    currentIndex = 0;
                } else {
                    currentIndex = index;
                }
                
                slidesContainer.style.transform = `translateX(-${currentIndex * 100}%)`;
                updateIndicators();
            }
            
            // Update indicators
            function updateIndicators() {
                indicators.forEach((indicator, index) => {
                    if (index === currentIndex) {
                        indicator.classList.add('active', 'bg-gray-800');
                        indicator.classList.remove('bg-gray-400');
                    } else {
                        indicator.classList.remove('active', 'bg-gray-800');
                        indicator.classList.add('bg-gray-400');
                    }
                });
            }
            
            // Event listeners
            prevBtn.addEventListener('click', () => goToSlide(currentIndex - 1));
            nextBtn.addEventListener('click', () => goToSlide(currentIndex + 1));
            
            indicators.forEach((indicator, index) => {
                indicator.addEventListener('click', () => goToSlide(index));
            });
        }
        
        // Initialize 360 viewer
        function initializeViewer() {
            if (document.getElementById('viewer') && typeof window.Viewer === 'undefined') {
                import('@photo-sphere-viewer/core').then(module => {
                    const { Viewer } = module;
                    const panoramas = @json($attraction['panorama_images'] ?? []);
                    
                    if (panoramas.length > 0) {
                        viewer = new Viewer({
                            container: document.querySelector('#viewer'),
                            panorama: panoramas[0].url,
                            caption: panoramas[0].caption,
                            loadingImg: 'https://photo-sphere-viewer-data.netlify.app/assets/loader.gif',
                            touchmoveTwoFingers: true,
                            mousewheelCtrlKey: true,
                        });
                        
                        // Add navigation buttons if multiple panoramas
                        if (panoramas.length > 1) {
                            const container = document.querySelector('#viewer').parentElement;
                            const navDiv = document.createElement('div');
                            navDiv.className = 'flex justify-center mt-4 gap-4';
                            
                            panoramas.forEach((panorama, index) => {
                                const btn = document.createElement('button');
                                btn.className = 'px-4 py-2 bg-primary text-white rounded hover:bg-primary-dark transition';
                                btn.textContent = 'View ' + (index + 1);
                                btn.onclick = () => {
                                    viewer.setPanorama(panorama.url, { caption: panorama.caption });
                                };
                                navDiv.appendChild(btn);
                            });
                            
                            container.appendChild(navDiv);
                        }
                    }
                });
            }
        }
        
        // Initialize first tab
        const firstActiveTab = document.querySelector('.tab-btn.active');
        if (firstActiveTab) {
            const targetId = firstActiveTab.getAttribute('data-target');
            if (targetId === 'experience-content') {
                // Small delay to ensure DOM is fully loaded
                setTimeout(initializeViewer, 100);
            }
        }
    });

    // Create TTS object when page loads
    document.addEventListener('DOMContentLoaded', async () => {
        const tts = new TextToSpeech();
        await tts.init();

        // Add event listener for play button
        const ttsButton = document.getElementById('ttsButton');
        if (ttsButton) {
            ttsButton.addEventListener('click', () => {
                const description = document.getElementById('attractionDescription').textContent;
                if (tts.isPlaying) {
                    tts.stop();
                } else {
                    tts.speak(description);
                }
            });
        }
    });
    </script>

    <style>
    /* Tab styling */
    .tab-btn {
        transition: color 0.3s, border-color 0.3s;
    }

    .tab-btn.active {
        border-bottom-color: rgba(210, 172, 113, 1); /* Primary color */
        color: rgba(210, 172, 113, 1); /* Primary color */
        border-bottom-width: 2px;
    }

    .tab-btn:not(.active) {
        border-bottom-color: transparent;
        color: #666;
    }

    .tab-btn:hover:not(.active) {
        border-bottom-color: #ddd;
        color: #444;
    }
    </style>


    @if (isset($related) && count($related) > 0)
        <div class="mb-12">
            <h2 class="text-2xl font-bold mb-6 text-gray-600">You Might Also Like</h2>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach ($related as $relatedAttraction)
                    <div class="card">
                        <div class="relative">
                            <img src="{{ $relatedAttraction['image'] }}" alt="{{ $relatedAttraction['title'] }}"
                                class="w-full h-48 object-cover">
                            <div class="absolute top-4 left-4 bg-white/80 backdrop-blur-sm px-3 py-1 rounded-full">
                                <p class="text-gray-600 font-medium">
                                    From {{ $relatedAttraction['price'] }}£E<span class="text-sm">/person</span>
                                </p>
                            </div>
                        </div>

                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <h3 class="text-xl font-bold text-gray-600">{{ $relatedAttraction['title'] }}</h3>
                                <div class="flex items-center gap-1">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 star-rating"
                                        viewBox="0 0 20 20" fill="currentColor">
                                        <path
                                            d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118l-2.8-2.034c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z" />
                                    </svg>
                                    <span class="text-sm text-gray-600">{{ $relatedAttraction['rating'] }}</span>
                                </div>
                            </div>
                            <a href="{{ route('attractions.show', $relatedAttraction['slug']) }}"
                                class="btn-primary w-full">View Details</a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    @push('scripts')
    <script src="{{ asset('js/tts.js') }}"></script>
    @endpush
@endsection
