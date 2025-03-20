<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Joyhub Dashboard</title>
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- Styles -->
    @vite(['resources/css/app.css'])
</head>
<body>
    <div class="dashboard">
        <div class="header">
            <div class="logo"><a href="/">joyhub</a></div>
            <div class="search-bar">
                <input type="text" placeholder="Search" />
            </div>
            <div class="user-profile">
                @livewire('user-profile-dropdown')
            </div>
        </div>
        <div class="content">
            <x-tags-sidebar :tags="$tags"/>
        @if($images->isNotEmpty())
            <div class="main">
                <div class="gallery">
                    @foreach ($images as $image)
                        <div class="gallery-item">
                            <a  href="{{ route('artwork.show', $image->slug) }}">
                                <img src="{{ $image->thumbnail }}" alt="{{ $image->title }}" loading="lazy" class="gallery-image" />
                            </a>
                            <p>{{ $image->title }}</p>
                        </div>
                    @endforeach
                        <div class="pagination">
                            {{ $images->links() }}
                        </div>
                </div>
            </div>
                @else
                    <div class="main">
                        <div class="gallery">
                                <div class="">
                                    <img src="" alt="" />
                                    <p></p>
                                </div>
                        </div>
                    </div>
            @endif
        </div>
    </div>
</body>
</html>
