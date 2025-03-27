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
    <header class="header">
        <div class="logo"><a href="/">joyhub</a></div>
        <div class="search-bar">
            <form action="{{ route('welcome') }}" method="GET" id="searchForm">
                <input type="text"  class="register-form-input" name="search" placeholder="Search tags"
                       id="searchInput" value="{{ request('search') }}" autocomplete="off" />
                <div id="search-suggestions" class="search-suggestions"></div>
                <button type="submit">Search</button>
            </form>
        </div>
        <div class="user-profile">
            @livewire('user-profile-dropdown')
        </div>
    </header>
    <div class="content">
        <x-tags-sidebar :tags="$tags"/>
        @if($images->isNotEmpty())
            <div class="main">
                <select name="type" class="filter" id="typeFilter">
                    <option value="">All types</option>
                    <option value="image" {{ request('type') == 'image' ? 'selected' : '' }}>Images</option>
                    <option value="video" {{ request('type') == 'video' ? 'selected' : '' }}>Videos</option>
                </select>
                <select name="sort" class="filter" id="sortFilter">
                    <option value="">Sort by</option>
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Newest</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Oldest</option>
                </select>
                <div class="gallery">
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
                        {{ $images->links() }}
                    </div>
                </div>
            </div>
        @else
            <div class="main">
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
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const suggestionsContainer = document.getElementById('search-suggestions');
        const searchForm = document.getElementById('searchForm');
        const typeFilter = document.getElementById('typeFilter');
        const sortFilter = document.getElementById('sortFilter');

        // Function to trigger the search
        function triggerSearch() {
            searchForm.submit();
        }

        // Fetch search suggestions
        searchInput.addEventListener('input', async function () {
            const query = searchInput.value.trim();

            if (query.length > 1) {
                try {
                    const response = await fetch(`/tags/search?query=${query}`);
                    if (!response.ok) throw new Error('Network response was not ok');
                    const tags = await response.json();

                    // Clear previous suggestions
                    suggestionsContainer.innerHTML = '';

                    // If tags are found, show them
                    if (tags.length > 0) {
                        suggestionsContainer.classList.add('visible');
                        tags.forEach(tag => {
                            const suggestion = document.createElement('div');
                            suggestion.className = 'p-2 hover:bg-gray-200 cursor-pointer';
                            suggestion.innerText = tag.name;
                            suggestion.addEventListener('click', function () {
                                searchInput.value = tag.name;
                                suggestionsContainer.classList.remove('visible');
                                triggerSearch();
                            });
                            suggestionsContainer.appendChild(suggestion);
                        });
                    } else {
                        suggestionsContainer.classList.remove('visible'); // Hide if no tags
                    }
                } catch (error) {
                    console.error('Error fetching tags:', error);
                    suggestionsContainer.classList.remove('visible'); // Hide on error
                }
            } else {
                suggestionsContainer.classList.remove('visible'); // Hide if query is short
            }
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', function (event) {
            if (!searchInput.contains(event.target) && !suggestionsContainer.contains(event.target)) {
                suggestionsContainer.classList.remove('visible');
            }
        });

        // Trigger search when dropdown selection changes
        typeFilter.addEventListener('change', triggerSearch);
        sortFilter.addEventListener('change', triggerSearch);

        // Trigger search when "Enter" key is pressed in the search input
        searchInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();  // Prevent default form submission
                triggerSearch();
            }
        });
    });
</script>
</html>
