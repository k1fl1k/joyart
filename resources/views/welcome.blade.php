<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}?v=3">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Joyhub</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Styles -->
    @vite(['resources/css/app.css'])
</head>
<body>
<div class="dashboard">
    <x-header :tags="$tags" />
    <div class="content">
        <x-tags-sidebar :tags="$tags"/>
        @if($images->isNotEmpty())
            <div class="main">


                <div class="gallery">
                    <div class="profile-header">
                        <div class="filter-bar">
                            <!-- Filter Dropdown -->
                            <select name="filter" class="rounded-lg bg-gray-800" id="filterDropdown">
                                <option value="">All types</option>
                                <option value="newest" {{ request('filter') == 'newest' ? 'selected' : '' }}>Новіші</option>
                                <option value="oldest" {{ request('filter') == 'oldest' ? 'selected' : '' }}>Старіші</option>
                                <option value="image" {{ request('filter') == 'image' ? 'selected' : '' }}>Тільки зображення</option>
                                <option value="video" {{ request('filter') == 'video' ? 'selected' : '' }}>Тільки відео</option>
                            </select>
                        </div>
                    </div>
                    @foreach ($images as $image)
                        <div class="gallery-item">
                            <a href="{{ route('artwork.show', $image->slug) }}">
                                <img src="{{ Str::startsWith($image->thumbnail, 'http') ? $image->thumbnail : asset($image->thumbnail) }}"
                                     alt="{{ $image->title }}" loading="lazy" class="gallery-image" />
                            </a>
                            <p>{{ $image->title }}</p>
                        </div>
                    @endforeach
                        <div class="pagination">
                            {{ $images->appends(['filter' => request('filter')])->links() }}
                        </div>
                </div>
            </div>
        @else
            <div class="main">
                <div class="filter-bar">
                    <!-- Filter Dropdown -->
                    <select name="filter" class="filter" id="filterDropdown">
                        <option value="">All types</option>
                        <option value="newest" {{ request('filter') == 'newest' ? 'selected' : '' }}>Новіші</option>
                        <option value="oldest" {{ request('filter') == 'oldest' ? 'selected' : '' }}>Старіші</option>
                        <option value="image" {{ request('filter') == 'image' ? 'selected' : '' }}>Тільки зображення</option>
                        <option value="video" {{ request('filter') == 'video' ? 'selected' : '' }}>Тільки відео</option>
                    </select>
                </div>
                <div class="gallery">
                    <div class="">
                        <img src="" alt="" />
                        <p>No images found</p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</div>
</body>
</html>
