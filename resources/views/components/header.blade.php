<!-- resources/views/components/header.blade.php -->
<header class="header">
    <div class="logo"><a href="/">joyhub</a></div>
    <button class="mobile-menu-button" id="mobileMenuButton">
        <i class="fas fa-bars"></i>☰
    </button>
    <div class="search-bar">
        <form action="{{ route('welcome') }}" method="GET" id="searchForm">
            <input type="text" class="register-form-input" name="search" placeholder="Search tags"
                   id="searchInput" value="{{ request('search') }}" autocomplete="off" />
            <div id="search-suggestions" class="search-suggestions"></div>
            <button type="submit">➤</button>
        </form>
    </div>
    <div class="user-profile">
        @livewire('user-profile-dropdown')
    </div>
</header>
<div class="mobile-menu" id="mobileMenu">
    <div class="mobile-search">
        <form action="{{ route('welcome') }}" method="GET">
            <input type="text" class="register-form-input" name="search" placeholder="Search tags" value="{{ request('search') }}" />
            <button type="submit">➤</button>
        </form>
    </div>
    <div class="mobile-nav">
        @auth
            <a href="{{ route('profile.show', auth()->user()->username) }}" class="mobile-nav-link">
                <span class="mobile-nav-icon">👤</span> My Profile
            </a>
            <a href="{{ route('welcome') }}" class="mobile-nav-link">
                <span class="mobile-nav-icon">🏠</span> Home Page
            </a>
            <a href="{{ route('settings.show') }}" class="mobile-nav-link">
                <span class="mobile-nav-icon">⚙️</span> Settings
            </a>
            <form method="POST" action="{{ route('logout') }}" class="mobile-nav-link">
                @csrf
                <button type="submit" class="mobile-nav-button">
                    <span class="mobile-nav-icon">→️</span> Logout
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="mobile-nav-link">
                <span class="mobile-nav-icon">🔒</span> Login
            </a>
            <a href="{{ route('register') }}" class="mobile-nav-link">
                <span class="mobile-nav-icon">✍️</span> Register
            </a>
        @endauth
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');
        const suggestionsContainer = document.getElementById('search-suggestions'); // <-- ось так
        const filterDropdown = document.getElementById('filterDropdown');
        const mobileMenuButton = document.getElementById('mobileMenuButton');
        const mobileMenu = document.getElementById('mobileMenu');

        // Mobile menu toggle
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('active');
            });

            // Закриваємо меню при кліку поза ним
            document.addEventListener('click', function(event) {
                if (!mobileMenu.contains(event.target) && !mobileMenuButton.contains(event.target) && mobileMenu.classList.contains('active')) {
                    mobileMenu.classList.remove('active');
                }
            });
        }

        // Закриваємо меню при кліку на посилання в ньому
        const mobileNavLinks = document.querySelectorAll('.mobile-nav-link');
        mobileNavLinks.forEach(link => {
            link.addEventListener('click', function() {
                if (mobileMenu.classList.contains('active')) {
                    mobileMenu.classList.remove('active');
                }
            });
        });

        function triggerSearch() {
            searchForm.submit();
        }

        searchInput.addEventListener('input', async function () {
            const query = searchInput.value.trim();

            if (query.length > 1) {
                try {
                    const response = await fetch(`/tags/search?query=${query}`);
                    if (!response.ok) throw new Error('Network response was not ok');
                    const tags = await response.json();

                    suggestionsContainer.innerHTML = '';

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
                        suggestionsContainer.classList.remove('visible');
                    }
                } catch (error) {
                    console.error('Error fetching tags:', error);
                    suggestionsContainer.classList.remove('visible');
                }
            } else {
                suggestionsContainer.classList.remove('visible');
            }
        });

        document.addEventListener('click', function (event) {
            if (!searchInput.contains(event.target) && !suggestionsContainer.contains(event.target)) {
                suggestionsContainer.classList.remove('visible');
            }
        });

        // Apply filter by changing URL and reloading the page
        filterDropdown.addEventListener('change', function () {
            const filterValue = filterDropdown.value;
            const currentUrl = new URL(window.location);

            // Set or remove the filter query parameter
            if (filterValue) {
                currentUrl.searchParams.set('filter', filterValue);
            } else {
                currentUrl.searchParams.delete('filter');
            }

            // Update the URL and reload the page
            window.location.href = currentUrl.toString();
        });

        // Preserve the filter on pagination
        const paginationLinks = document.querySelectorAll('.pagination a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function (event) {
                const currentUrl = new URL(link.href);
                const filterValue = filterDropdown.value;

                if (filterValue) {
                    currentUrl.searchParams.set('filter', filterValue);
                }

                // Update the URL with the current filter before navigating
                event.preventDefault();
                window.location.href = currentUrl.toString();
            });
        });

        // Trigger search when "Enter" key is pressed in the search input
        searchInput.addEventListener('keydown', function (event) {
            if (event.key === 'Enter') {
                event.preventDefault();  // Prevent default form submission
                triggerSearch();
            }
        });
    });
</script>
