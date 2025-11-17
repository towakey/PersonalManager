@extends('layouts.app')

@section('title', __('plugins.title'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-6xl mx-auto">
        <!-- ヘッダー -->
        <div class="mb-8">
            <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ __('plugins.title') }}</h1>
            <p class="text-gray-600">{{ __('plugins.description') }}</p>
        </div>

        <!-- 成功メッセージ -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-green-100 border border-green-400 text-green-700 rounded">
                {{ session('success') }}
            </div>
        @endif

        <!-- プラグイン管理コンポーネント -->
        <livewire:plugin-management />
    </div>
</div>
@endsection
