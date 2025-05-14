@extends('layouts.app')

@section('title', 'All Articles')

@section('content')
<div class="container mx-auto px-4 py-8">
    <h1 class="text-3xl font-bold text-gray-700 mb-8">All Articles</h1>
    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
        @forelse($articles as $article)
            <div class="card group hover:shadow-xl transition-all duration-300">
                <div class="relative overflow-hidden">
                    <img src="{{ $article->Img ? '/storage/' . $article->Img : '/images/placeholder.jpg' }}" alt="{{ $article->ArticleHeading }}" class="w-full h-48 object-cover transition-transform duration-500 group-hover:scale-110">
                </div>
                <div class="p-4">
                    <h2 class="text-xl font-bold text-gray-600 mb-2">{{ $article->ArticleHeading }}</h2>
                    <p class="text-gray-600 mb-4 line-clamp-2">{{ Str::limit(strip_tags($article->ArticleBody), 120) }}</p>
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
</div>
@endsection 