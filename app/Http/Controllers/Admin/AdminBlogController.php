<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreBlogRequest;
use App\Http\Requests\Admin\UpdateBlogRequest;
use App\Models\Blog;
use App\Models\Cat;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class AdminBlogController extends Controller
{
    // ブログ一覧画面
    public function index()
    {
        $blogs = Blog::with(['user', 'category', 'cats'])
            ->orderBy('updated_at', 'desc')
            ->paginate(10);
        return view('admin.blogs.index', ['blogs' => $blogs]);
    }

    // ブログ投稿画面
    public function create()
    {
        $user = Auth::user();
        if (!$user->canCreateBlogs()) {
            abort(403, 'ブログを投稿する権限がありません。');
        }
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
        $user = Auth::user();
        if (!$user->canCreateBlogs()) {
            abort(403, 'ブログを投稿する権限がありません。');
        }
        $validated = $request->validated();
        $validated['image'] = $request->file('image')->store('blogs', 'public');
        $validated['user_id'] = Auth::id(); // 追加
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
        // 新しい権限チェック関数を使用
        if (!can_edit_blog($blog)) {
            abort(403, 'このブログを編集する権限がありません。');
        }
        
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
        $blog = Blog::with(['user', 'category', 'cats'])->findOrFail($id);
        
        // 新しい権限チェック関数を使用
        if (!can_edit_blog($blog)) {
            abort(403, 'このブログを更新する権限がありません。');
        }
        
        $validated = $request->validated();
        if ($request->has('image')) {
            Storage::disk('public')->delete($blog->image);
            $validated['image'] = $request->file('image')->store('blogs', 'public');
        }
        $validated['user_id'] = Auth::id(); // 追加
        $blog->category()->associate($validated['category_id']);
        $blog->update($validated);
        $blog->cats()->sync($validated['cats'] ?? []);

        return to_route('admin.blogs.index')->with('success', 'ブログを更新しました');
    }

    // 指定したIDのブログ削除処理
    public function destroy(string $id)
    {
        $blog = Blog::with(['user', 'category', 'cats'])->findOrFail($id);
        
        // 新しい権限チェック関数を使用
        if (!can_delete_blog($blog)) {
            abort(403, 'このブログを削除する権限がありません。');
        }
        
        $blog->cats()->detach();
        $blog->delete();
        Storage::disk('public')->delete($blog->image);

        return to_route('admin.blogs.index')->with('success', 'ブログを削除しました');
    }
}
