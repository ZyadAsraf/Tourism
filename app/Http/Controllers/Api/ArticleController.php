<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    /**
     * Get a list of all articles
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $articles = Article::with(['admin:id,name', 'attractions:id,AttractionName,City,Img'])
            ->latest()
            ->get();
            
        return response()->json([
            'success' => true,
            'data' => $articles,
        ]);
    }

    /**
     * Get a single article by ID
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        try {
            $article = Article::with(['admin:id,name', 'attractions:id,AttractionName,City,Img'])
                ->findOrFail($id);
            
            return response()->json([
                'success' => true,
                'data' => $article,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found',
            ], 404);
        }
    }

    /**
     * Store a new article
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'ArticleHeading' => 'required|string|max:255',
            'ArticleBody' => 'required|string',
            'Img' => 'nullable|string',
            'AdminId' => 'required|integer|exists:users,id',
            'attractions' => 'nullable|array',
            'attractions.*' => 'integer|exists:attractions,id',
        ]);
        
        $article = Article::create([
            'ArticleHeading' => $validated['ArticleHeading'],
            'ArticleBody' => $validated['ArticleBody'],
            'Img' => $validated['Img'] ?? null,
            'AdminId' => $validated['AdminId'],
        ]);
        
        // Attach attractions if provided
        if (isset($validated['attractions']) && count($validated['attractions']) > 0) {
            $article->attractions()->attach($validated['attractions']);
        }
        
        return response()->json([
            'success' => true,
            'data' => $article->load(['admin:id,name', 'attractions:id,AttractionName,City,Img']),
            'message' => 'Article created successfully',
        ], 201);
    }

    /**
     * Update an existing article
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $article = Article::findOrFail($id);
            
            $validated = $request->validate([
                'ArticleHeading' => 'sometimes|required|string|max:255',
                'ArticleBody' => 'sometimes|required|string',
                'Img' => 'nullable|string',
                'AdminId' => 'sometimes|required|integer|exists:users,id',
                'attractions' => 'nullable|array',
                'attractions.*' => 'integer|exists:attractions,id',
            ]);
            
            $article->update([
                'ArticleHeading' => $validated['ArticleHeading'] ?? $article->ArticleHeading,
                'ArticleBody' => $validated['ArticleBody'] ?? $article->ArticleBody,
                'Img' => $validated['Img'] ?? $article->Img,
                'AdminId' => $validated['AdminId'] ?? $article->AdminId,
            ]);
            
            // Sync attractions if provided
            if (isset($validated['attractions'])) {
                $article->attractions()->sync($validated['attractions']);
            }
            
            return response()->json([
                'success' => true,
                'data' => $article->fresh()->load(['admin:id,name', 'attractions:id,AttractionName,City,Img']),
                'message' => 'Article updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found',
            ], 404);
        }
    }

    /**
     * Delete an article
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            $article = Article::findOrFail($id);
            $article->delete();
            
            return response()->json([
                'success' => true,
                'message' => 'Article deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Article not found',
            ], 404);
        }
    }
}
