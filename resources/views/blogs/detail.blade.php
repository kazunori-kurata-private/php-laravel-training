@extends('layouts.default')
@section('title', 'ブログ詳細')

@section('content')
<section class="bg-gray-100 pt-2">
  <div class="container mx-auto">
    <p class="text-left px-4 pt-2 text-gray-400"><a href="{{ route('blogs.index') }}" class="text-blue-600 hover:underline">ホーム</a><span class="px-2">&gt</span><a href="{{ route('blogs.index', request()->query()) }}" class="text-blue-600 hover:underline">ブログ</a><span class="px-2">&gt</span>{{ $blog->title }}</p>
    <h1 class="mt-2 text-4xl font-bold font-heading h-40 text-center p-12">{{ $blog->title }}</h1>
  </div>
</section>

<section>
  <div class="container mx-auto">
    <div class="my-8 pb-4 border-b">
      <p class="text-lg text-left">
        @if($blog->category)
          {{ $blog->category->name }}
        @endif
        @if($blog->cats->count() > 0)
          / 
          @foreach($blog->cats as $cat)
            ＃{{ $cat->name }}
            @if(!$loop->last), @endif
          @endforeach
        @endif
      </p>
      <ul class="flex text-center pt-2">
        @if($blog->category)
          <li class="bg-gray-100 text-gray-400 py-1 px-3 mr-3">{{ $blog->category->name }}</li>
        @endif
        @foreach($blog->cats->take(5) as $cat)
          <li class="bg-gray-100 text-gray-400 py-1 px-3 mr-3">＃{{ $cat->name }}</li>
        @endforeach
      </ul>
    </div>
    <div>
      <img class="mb-12 rounded object-cover mx-auto" src="{{ $blog->image ? asset('storage/' . $blog->image) : '/images/placeholders/blogs/working.jpeg' }}" alt="{{ $blog->title }}">
    </div>
    <p class="text-gray-500 leading-loose">{{ $blog->body }}</p>
  </div>
</section>

<section class="mt-24">
  <div class="container mx-auto">
    <h2 class="mb-2 leading-tight font-medium font-heading text-2xl">この記事を書いた店員</h2>
    <div class="mb-10 pt-5 flex">
      @if($blog->user)
        <img src="{{ $blog->user->image ? asset('storage/' . $blog->user->image) : '/images/placeholders/admin/user.jpg' }}" alt="" class="h-40 w-40 rounded-full shadow">
        <div class="pl-10">
          <h4 class="text-xl font-medium">{{ $blog->user->introduction }}</h4>
          <p class="pt-3">{{ $blog->user->name }}</p>
        </div>
      @else
        <img src="/images/placeholders/admin/user.jpg" alt="" class="h-40 w-40 rounded-full shadow">
        <div class="pl-10">
          <h4 class="text-xl font-medium">（投稿者情報なし）</h4>
        </div>
      @endif
    </div>
  </div>
</section>

<section class="mt-16 pb-24">
  <div class="container mx-auto">
    <h3 class="mb-2 leading-tight font-medium font-heading text-center text-2xl">この店員が書いた他の記事</h3>

    <div class="flex flex-wrap -mx-3 pt-4">
      @forelse($relatedBlogs as $relatedBlog)
        <article class="w-full md:w-1/2 lg:w-1/3 p-3">
          <div class="border rounded-lg overflow-hidden shadow">
            <div class="relative h-52">
              @if($relatedBlog->category)
                <span class="absolute text-xs text-gray-400 px-2 border border-white bg-gray-100 uppercase py-2 px-10 left-0 bottom-0">{{ $relatedBlog->category->name }}</span>
              @endif
              <a href="{{ route('blogs.show', $relatedBlog) }}">
                <img class="w-full h-full object-cover" src="{{ $relatedBlog->image ? asset('storage/' . $relatedBlog->image) : '/images/placeholders/blogs/lunch.jpeg' }}" alt="{{ $relatedBlog->title }}">
              </a>
              <time class="text-xs text-gray-500 text-right pr-2 pt-2">{{ $relatedBlog->created_at->format('Y.m.d') }}</time>
            </div>
            <div class="pt-2 pb-4 px-4">
              <a href="{{ route('blogs.show', $relatedBlog) }}">
                <h1 class="mb-2 text-2xl font-semibold font-heading text-left">{{ $relatedBlog->title }}</h1>
                <p class="mb-6 text-gray-500 leading-relaxed text-left truncate">{{ Str::limit($relatedBlog->body, 100) }}</p>
              </a>
              <div class="flex justify-between">
                <ul class="flex">
                  @foreach($relatedBlog->cats->take(3) as $cat)
                    <li class="bg-gray-100 text-gray-400 text-xs mr-2 py-1 px-2">＃{{ $cat->name }}</li>
                  @endforeach
                </ul>
                <p class="font-medium font-semibold">{{ $relatedBlog->user->name ?? '不明' }}</p>
              </div>
            </div>
          </div>
        </article>
      @empty
        <div class="w-full text-center py-8">
          <p class="text-gray-500">関連記事が見つかりませんでした。</p>
        </div>
      @endforelse
    </div>
    <div class="mt-8 text-center">
      <a href="{{ route('blogs.index', request()->query()) }}" class="inline-block text-xs text-white font-semibold leading-none rounded py-4 text-blue-500 border-solid bg-white border-blue-500 border-2 px-16 hover:text-white hover:bg-blue-500">もっと見る</a>
    </div>
  </div>
</section>
@endsection