<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;
use Illuminate\Support\Str;

class ArticlesController extends Controller
{
    // List all articles
    public function index()
    {
        $articles = Article::with('admin')->latest()->get();
        return view('articles.index', compact('articles'));
    }

    // Show a single article
    public function show($id)
    {
        $article = Article::with(['admin', 'attractions'])->findOrFail($id);
        return view('articles.show', compact('article'));
    }

    // Show form to create a new article
    public function create()
    {
        return view('articles.create');
    }

    // Store a new article
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ArticleLinks' => 'nullable|string',
            'ArticleHeading' => 'required|string|max:255',
            'ArticleBody' => 'required|string',
            'Img' => 'nullable|string',
            'AdminId' => 'required|integer|exists:users,id',
        ]);
        $article = Article::create($validated);
        return redirect()->route('articles.show', $article->id);
    }

    // Show form to edit an article
    public function edit($id)
    {
        $article = Article::findOrFail($id);
        return view('articles.edit', compact('article'));
    }

    // Update an article
    public function update(Request $request, $id)
    {
        $article = Article::findOrFail($id);
        $validated = $request->validate([
            'ArticleLinks' => 'nullable|string',
            'ArticleHeading' => 'required|string|max:255',
            'ArticleBody' => 'required|string',
            'Img' => 'nullable|string',
            'AdminId' => 'required|integer|exists:users,id',
        ]);
        $article->update($validated);
        return redirect()->route('articles.show', $article->id);
    }

    // Delete an article
    public function destroy($id)
    {
        $article = Article::findOrFail($id);
        $article->delete();
        return redirect()->route('articles.index');
    }
} 