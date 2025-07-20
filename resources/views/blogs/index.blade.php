@extends('layouts.default')
@section('title', 'ブログ一覧')

@section('content')
<section class="bg-gray-100 pt-2">
    <div class="container mx-auto">
        <p class="text-left px-4 pt-2 text-gray-400"><a href="#" class="text-blue-600 hover:underline">ホーム</a><span class="px-2">&gt</span>ブログ</p>
        <p class="text-center pt-10 text-2xl">ブログ</p>
        <h1 class="mt-2 text-4xl font-bold font-heading text-center h-32">ほぼ毎日お店でねこの様子をお届け！！</h1>
    </div>
</section>

<section class="pb-24">
    <div class="container px-4 mx-auto">
        <div class="my-8 pb-4 border-b">
            <p class="text-lg text-left">カテゴリ / ねこちゃん</p>
            @if(request('category'))
                @php
                    $selectedCategory = $categories->find(request('category'));
                @endphp
                @if($selectedCategory)
                    <p class="text-sm text-gray-600 mb-2">現在のフィルター: <span class="font-semibold">{{ $selectedCategory->name }}</span></p>
                @endif
            @endif
            <ul class="flex text-center pt-2">
                <li class="{{ !request('category') ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-400' }} py-1 px-3 mr-3">
                    <a href="{{ route('blogs.index', array_filter(array_merge(request()->query(), ['page' => 1]), function($value, $key) { return $key !== 'category'; }, ARRAY_FILTER_USE_BOTH)) }}">全カテゴリー</a>
                </li>
                @foreach($categories as $category)
                    <li class="{{ request('category') == $category->id ? 'bg-blue-500 text-white' : 'bg-gray-100 text-gray-400' }} py-1 px-3 mr-3">
                        <a href="{{ request('category') == $category->id 
                            ? route('blogs.index', array_filter(array_merge(request()->query(), ['page' => 1]), function($value, $key) { return $key !== 'category'; }, ARRAY_FILTER_USE_BOTH))
                            : route('blogs.index', array_merge(request()->query(), ['category' => $category->id, 'page' => 1]))
                        }}">{{ $category->name }}</a>
                    </li>
                @endforeach
            </ul>
        </div>

        <div class="flex flex-wrap -mx-3">
            @forelse($blogs as $blog)
                <article class="w-full md:w-1/2 lg:w-1/3 p-3">
                    <div class="border rounded-lg overflow-hidden shadow">
                        <div class="relative h-52">
                            @if($blog->category)
                                <span class="py-2 px-10 mt-56 absolute left-0 bottom-0 text-xs text-gray-400 px-2 border border-white bg-gray-100 uppercase">{{ $blog->category->name }}</span>
                            @endif
                            <a href="{{ route('blogs.show', array_merge([$blog], request()->query())) }}">
                                <img class="w-full h-56 object-cover" src="{{ $blog->image ? asset('storage/' . $blog->image) : '/images/placeholders/blogs/lunch.jpeg' }}" alt="{{ $blog->title }}">
                            </a>
                            <time class="block text-xs text-gray-500 text-right pt-1 pr-2">{{ $blog->created_at->format('Y.m.d') }}</time>
                        </div>
                        <div class="pt-2 pb-4 px-4">
                            <a href="{{ route('blogs.show', array_merge([$blog], request()->query())) }}">
                                <h1 class="mb-2 text-2xl font-semibold font-heading text-left">{{ $blog->title }}</h1>
                                <p class="mb-6 text-gray-500 leading-relaxed text-left truncate">{{ Str::limit($blog->body, 100) }}</p>
                            </a>
                            <div class="flex justify-between">
                                <ul class="flex">
                                    @foreach($blog->cats->take(3) as $cat)
                                        <li class="bg-gray-100 text-gray-400 text-xs mr-2 py-1 px-2">＃{{ $cat->name }}</li>
                                    @endforeach
                                </ul>
                                <p class="font-medium font-semibold">{{ $blog->user->name ?? '不明' }}</p>
                            </div>
                        </div>
                    </div>
                </article>
            @empty
                <div class="w-full text-center py-8">
                    <p class="text-gray-500">記事が見つかりませんでした。</p>
                </div>
            @endforelse
        </div>

        @if($blogs->hasPages())
            <div class="mt-8 text-center">
                {{ $blogs->links() }}
            </div>
        @endif

</section>
@endsection