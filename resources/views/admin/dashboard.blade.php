@extends('layouts.admin')

@section('content')
<section class="py-8">
    <div class="container px-4 mx-auto">
        <div class="py-4 bg-white rounded">
            <div class="flex px-6 pb-4 border-b">
                <h2 class="text-xl font-bold">ダッシュボード</h2>
            </div>
            <div class="pt-4 px-6">
                <div class="text-center py-12">
                    <div class="text-6xl text-gray-300 mb-4">
                        <i class="fas fa-home"></i>
                    </div>
                    <h3 class="text-2xl font-semibold text-gray-700 mb-2">
                        管理画面へようこそ
                    </h3>
                    <p class="text-gray-500">
                        左側のメニューから操作したい機能を選択してください
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>
@endsection
