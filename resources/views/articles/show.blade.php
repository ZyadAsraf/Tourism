@extends('layouts.app')

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