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
        <div class="prose max-w-none mb-6">
            {!! nl2br(e($article->ArticleBody)) !!}
        </div>
        @if($article->admin)
            <div class="text-gray-500 text-sm mt-4">
                By {{ $article->admin->name }}
            </div>
        @endif
    </div>
</div>
@endsection 