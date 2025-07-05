<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlogRequest;
use App\Http\Requests\Admin\UpdateBlogRequest;
use App\Models\Blog;
use App\Models\Cat;
use App\Models\Category;
use Illuminate\Support\Facades\Storage;

class AdminBlogController extends Controller
{
    // ブログ一覧画面
    public function index()
    {
        $blogs = Blog::orderBy('updated_at', 'desc')
            ->paginate(10);
        return view('admin.blogs.index', ['blogs' => $blogs]);
    }

    // ブログ投稿画面
    public function create()
    {
        $categories = Category::all();
        $cats = Cat::all();
        return view('admin.blogs.create', [
            'categories' => $categories,
            'cats' => $cats
        ]);
    }

    // ブログ投稿処理
    public function store(StoreBlogRequest $request)
    {
        $validated = $request->validated();
        $validated['image'] = $request->file('image')->store('blogs', 'public');
        $blog = Blog::create($validated);
        $blog->category()->associate($validated['category_id']);
        $blog->cats()->sync($validated['cats'] ?? []);

        return to_route('admin.blogs.index')->with('success', 'ブログを投稿しました');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    // 指定したIDのブログ編集画面
    public function edit(Blog $blog)
    {
        $categories = Category::all();
        $cats = Cat::all();
        return view('admin.blogs.edit', [
            'blog'=> $blog,
            'categories' => $categories,
            'cats' => $cats
        ]);
    }

    // 指定したIDのブログ更新処理
    public function update(UpdateBlogRequest $request, string $id)
    {
        $blog = Blog::findOrFail($id);
        $validated = $request->validated();
        if ($request->has('image')) {
            Storage::disk('public')->delete($blog->image);
            $validated['image'] = $request->file('image')->store('blogs', 'public');
        }
        $blog->category()->associate($validated['category_id']);
        $blog->update($validated);
        $blog->cats()->sync($validated['cats'] ?? []);

        return to_route('admin.blogs.index')->with('success', 'ブログを更新しました');
    }

    // 指定したIDのブログ削除処理
    public function destroy(string $id)
    {
        $blog = Blog::findOrFail($id);
        $blog->cats()->detach();
        $blog->delete();
        Storage::disk('public')->delete($blog->image);

        return to_route('admin.blogs.index')->with('success', 'ブログを削除しました');
    }
}
