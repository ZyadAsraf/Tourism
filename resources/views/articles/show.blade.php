@extends('layouts.app')
@php
use Illuminate\Support\Str;
@endphp

@section('title', $article->ArticleHeading)

@section('content')
<div class="container mx-auto px-4 py-8 max-w-3xl">
    <div class="mb-6">
        <a href="{{ route('articles.index') }}" class="text-primary hover:underline">&larr; Back to Articles</a>
    </div>
    <div class="bg-white rounded-lg shadow-md p-6">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">{{ $article->ArticleHeading }}</h1>
        @if($article->Img)
            <img src="{{ '/storage/' . $article->Img }}" alt="{{ $article->ArticleHeading }}" class="w-full h-64 object-cover rounded mb-6">
        @endif
        <button id="ttsButton" class="btn-primary flex items-center gap-2">
            <i class="fas fa-volume-up"></i>
            <span>Listen to Article</span>
        </button>
        <div id="articleBody" class="prose max-w-none mb-6">
            {!! nl2br(e($article->ArticleBody)) !!}
        </div>
        
        @if($article->attractions->count() > 0)
            <div class="mt-8">
                <h2 class="text-xl font-semibold text-gray-800 mb-4">Related Attractions</h2>
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                    @foreach($article->attractions as $attraction)
                        <div class="bg-gray-100 rounded-lg overflow-hidden shadow-sm hover:shadow-md transition-shadow">
                            <a href="{{ route('attractions.show', Str::slug($attraction->AttractionName)) }}" class="block">
                                @if($attraction->Img)
                                    <img src="{{ '/storage/' . $attraction->Img }}" alt="{{ $attraction->AttractionName }}" class="w-full h-40 object-cover">
                                @else
                                    <div class="w-full h-40 bg-gray-300 flex items-center justify-center">
                                        <span class="text-gray-500">No Image</span>
                                    </div>
                                @endif
                                <div class="p-4">
                                    <h3 class="font-semibold text-gray-800">{{ $attraction->AttractionName }}</h3>
                                    @if($attraction->City)
                                        <p class="text-gray-600 text-sm">{{ $attraction->City }}</p>
                                    @endif
                                </div>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
        
        @if($article->admin)
            <div class="text-gray-500 text-sm mt-4">
                By {{ $article->admin->name }}
            </div>
        @endif
    </div>
</div>
<script>
    // Create TTS object when page loads
    document.addEventListener('DOMContentLoaded', async () => {
        const tts = new TextToSpeech();
        await tts.init();

        // Add event listener for play button
        const ttsButton = document.getElementById('ttsButton');
        if (ttsButton) {
            ttsButton.addEventListener('click', () => {
                const description = document.getElementById('articleBody').textContent;
                if (tts.isPlaying) {
                    tts.stop();
                } else {
                    tts.speak(description);
                }
            });
        }
    });
</script>
@push('scripts')
<script src="{{ asset('js/tts.js') }}"></script>
@endpush
@endsection 