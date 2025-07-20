<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\Category;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Blog::with(['category', 'cats', 'user']);
        
        // カテゴリフィルタリング
        if ($request->has('category') && $request->category) {
            $query->where('category_id', $request->category);
        }
        
        $blogs = $query->orderBy('created_at', 'desc')->paginate(9);
        
        // カテゴリーフィルターをページネーションリンクに追加
        if ($request->has('category') && $request->category) {
            $blogs->appends(['category' => $request->category]);
        }
        
        $categories = Category::all();
        
        return view('blogs.index', compact('blogs', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(Blog $blog)
    {
        $blog->load(['category', 'cats', 'user']);
        
        // 同じカテゴリの他の記事を取得
        $relatedBlogs = Blog::with(['category', 'cats'])
            ->where('category_id', $blog->category_id)
            ->where('id', '!=', $blog->id)
            ->orderBy('created_at', 'desc')
            ->limit(3)
            ->get();
        
        return view('blogs.detail', compact('blog', 'relatedBlogs'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Blog $blog)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Blog $blog)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Blog $blog)
    {
        //
    }
}
