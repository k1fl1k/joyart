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
                <div class="profile-circle">
                    <img src="{{ Auth::user()->avatar ??  asset('storage/images/avatar-male.png') }}" alt="User Avatar">
                </div>
                <div class="username">
                    <span>{{ Auth::user()->name ?? 'Username' }}</span>
                    <span class="role">role: {{ Auth::user()->role ?? 'User' }}</span>
                </div>
            </div>
        </div>
        <div class="content">
            @if($tags->isNotEmpty())
            <div class="sidebar">
                @foreach ($tags as $tag)
                    <div class="tag">
                        {{ $tag->name }}
                        @if ($tag->subtags->isNotEmpty())
                            <div class="subtags">
                                @foreach ($tag->subtags as $subtag)
                                    <div class="subtag">{{ $subtag->name }}</div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            @else
                <div class="sidebar">
                    <div class="tag">
                        <div class="subtags">
                            <div class="subtag"></div>
                        </div>
                    </div>
                </div>
            @endif
            @if($images->isNotEmpty())
            <div class="main">
                <div class="gallery">
                    @foreach ($images as $image)
                        <div class="gallery-item">
                            <img src="{{ $image->thumbnail }}" alt="{{ $image->title }}" class="gallery-image" />
                            <p>{{ $image->title }}</p>
                        </div>
                    @endforeach
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
