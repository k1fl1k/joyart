<!-- resources/views/components/header.blade.php -->
<header class="header">
    <div class="logo"><a href="/">joyhub</a></div>
    <div class="search-bar">
        <form action="{{ route('welcome') }}" method="GET" id="searchForm">
            <input type="text" class="register-form-input" name="search" placeholder="Search tags"
                   id="searchInput" value="{{ request('search') }}" autocomplete="off" />
            <div id="search-suggestions" class="search-suggestions"></div>
            <button type="submit">Search</button>
        </form>
    </div>
    <div class="user-profile">
        @livewire('user-profile-dropdown')
    </div>
</header>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const searchInput = document.getElementById('searchInput');
        const searchForm = document.getElementById('searchForm');
        const filterDropdown = document.getElementById('filterDropdown');

        // Function to handle search form submission
        function triggerSearch() {
            searchForm.submit();
        }

        // Handle search suggestions (no changes needed here)
        searchInput.addEventListener('input', async function () {
            const query = searchInput.value.trim();
            const suggestionsContainer = document.getElementById('search-suggestions');

            if (query.length > 1) {
                try {
                    const response = await fetch(`/tags/search?query=${query}`);
                    if (!response.ok) throw new Error('Network response was not ok');
                    const tags = await response.json();

                    // Clear previous suggestions
                    suggestionsContainer.innerHTML = '';

                    // Display search suggestions if available
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

        // Hide search suggestions when clicking outside the input
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
