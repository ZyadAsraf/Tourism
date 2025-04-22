@extends('layouts.app')

@section('title', $attraction['title'] . ' - Massar')

@section('content')
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
                          <path d=" M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371
                            1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54
                            1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0
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
            <h2 class="text-2xl font-bold mb-4 text-gray-600">About {{ $attraction['title'] }}</h2>
            <p class="text-gray-600 mb-6">{!! $attraction['description'] ?? 'No description available.' !!}</p>

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
                        <a href="{{ route('booking.form', $attraction['slug']) }}"
                            class="text-primary hover:underline">Book Now</a>
                    </div>

                    @guest
                        <p class="text-sm text-gray-500 text-center mt-4">You need to <a href="{{ route('login') }}"
                                class="text-primary">sign in</a> to complete your booking</p>
                    @endguest
                </form>
            </div>
        </div>
    </div>

    @if (isset($attraction['mapImage']))
        <div class="mb-12">
            <h2 class="text-2xl font-bold mb-4 text-gray-600">Location</h2>

        </div>
    @endif

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
@endsection
