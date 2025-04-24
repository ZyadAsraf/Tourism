

@extends('layouts.app')

@section('title', $attraction['title'] . ' - Massar')

@section('content')
<style>
    #star-rating .star {
        transition: color 0.2s ease, transform 0.2s ease;
    }

    #star-rating .star:hover {
        transform: scale(1.6);
    }

    #star-rating .star.hovered,
    #star-rating .star.active {
        color: #facc15; /* Tailwind yellow-400 */
    }
</style>

<div class="container py-8">
    <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-semibold text-gray-700 mb-6">Write a Review for {{ $attraction['title'] }}</h2>

        @if(session('success'))
            <div class="mb-4 text-green-600 font-medium">{{ session('success') }}</div>
        @endif


        <form action="{{ route('attractions.reviews.store', $attraction['slug']) }}" method="POST">
            @csrf
            <div class="mb-6">
                <div class="flex items-center gap-4">
                    <img src="{{ $attraction['image'] }}" alt="{{ $attraction['title'] }}"
                        class="w-20 h-20 rounded-full object-cover border border-gray-300">
                    <div>
                        <p class="text-gray-700 font-semibold text-lg">{{ Auth::user()->firstname }} {{Auth::user()->lastname}}</p>
                        <p class="text-sm text-gray-500">is reviewing this attraction</p>
                    </div>
                </div>
            </div>
            
            

            {{-- Rating --}}
            <div class="mb-4">
                <label class="block text-gray-600 mb-2">Your Rating</label>
                <div class="flex items-center space-x-1 text-yellow-400 text-2xl cursor-pointer" id="star-rating">
                    @for ($i = 1; $i <= 5; $i++)
                        <span data-rating="{{ $i }}" class="star hover:text-yellow-500">&#9733;</span>
                    @endfor
                </div>
                <input type="hidden" name="rating" id="rating" value="5">
                @error('rating')
                    <div class="text-red-500 text-sm">{{ $message }}</div>
                @enderror
            </div>

            {{-- Comment --}}
            <div class="mb-4">
                <label for="comment" class="block text-gray-600 mb-2">Your Review</label>
                <textarea name="comment" id="comment" rows="5" maxlength="500" class="w-full border border-gray-300 p-3 rounded focus:outline-none focus:border-primary" required></textarea>
                @error('comment')
                    <div class="text-red-500 text-sm">{{ $message }}</div>
                @enderror
            </div>

            {{-- Submit --}}
            <button type="submit" class="btn-primary px-6 py-2 rounded">Submit Review</button>
        </form>
    </div>
</div>

{{-- Star rating script --}}
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const stars = document.querySelectorAll('#star-rating .star');
        const ratingInput = document.getElementById('rating');

        let currentRating = parseInt(ratingInput.value);

        function updateStars(rating) {
            stars.forEach(star => {
                const starRating = parseInt(star.getAttribute('data-rating'));
                star.classList.toggle('active', starRating <= rating);
                star.innerHTML = starRating <= rating ? '★' : '☆';
            });
        }

        stars.forEach(star => {
            const starRating = parseInt(star.getAttribute('data-rating'));

            star.addEventListener('click', () => {
                currentRating = starRating;
                ratingInput.value = currentRating;
                updateStars(currentRating);
            });

            star.addEventListener('mouseover', () => {
                updateStars(starRating);
            });

            star.addEventListener('mouseout', () => {
                updateStars(currentRating);
            });
        });

        // Initial star display
        updateStars(currentRating);
    });
</script>

@endsection
