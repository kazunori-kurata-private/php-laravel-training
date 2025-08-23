<!DOCTYPE html>
<html lang="ja">
<head>
    <title>アクセス拒否</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <link rel="stylesheet" href="/css/tailwind/tailwind.min.css">
    <link rel="icon" type="image/png" sizes="16x16" href="/favicon.png">
</head>
<body class="antialiased bg-body text-body font-body">
<div>
    <section class="h-screen py-48 bg-blueGray-50">
        <div class="container px-4 mx-auto">
            <div class="flex max-w-md mx-auto flex-col text-center">
                <div class="mt-12 mb-8 p-8 bg-white rounded shadow">
                    <h1 class="mb-6 text-3xl text-red-600">アクセス拒否</h1>
                    
                    <div class="mb-8 py-4 px-6 border border-red-300 bg-red-50 rounded">
                        <p class="text-red-400 mb-4">
                            @if(isset($exception) && $exception->getMessage())
                                {{ $exception->getMessage() }}
                            @else
                                このページを閲覧する権限がありません。
                            @endif
                        </p>
                        <p class="text-sm text-gray-600">
                            管理者へ問い合わせてください。
                        </p>
                    </div>

                    <div class="flex justify-center space-x-4">
                        <a href="{{ route('admin.blogs.index') }}" class="py-2 px-4 text-sm text-blue-600 hover:text-blue-800 underline">
                            ブログ管理画面に戻る
                        </a>
                        <a href="{{ route('admin.login') }}" class="py-2 px-4 text-sm text-blue-600 hover:text-blue-800 underline">
                            ログイン画面
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
</div>
</body>
</html> 