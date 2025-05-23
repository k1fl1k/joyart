<x-app-layout>
    <div class="py-12">
        <div class="profile-container">
            <div class="profile-info">
                <!-- Аватар і ім'я -->
                <div class="profile-header">
                    <div class="profile-circle">
                        <img src="{{ $user->avatar ?? asset('storage/images/avatar-male.png') }}" alt="User Avatar">
                    </div>
                    <div>
                        <h2>{{ $user->username }}</h2>
                        <p class="text-gray-400">role: {{ $user->role }}</p>
                    </div>

                    <!-- Кнопка налаштувань (тільки для власника профілю) -->
                    @if(auth()->id() === $user->id)
                        <div class="settings">
                            <a href="{{ route('settings.show') }}" class="profile-settings-btn">Settings</a>
                        </div>
                    @endif
                </div>

                <!-- Блоки інформації -->
                <div class="profile-content">
                    <!-- Опис користувача -->
                    <div class="profile-description">
                        <h3>Description</h3>
                        <p>Birthday: {{ $user->birthday ?? 'Unknown date.'}}</p>

                        @if(auth()->id() === $user->id)
                            @if(session('editing_description'))
                                <form action="{{ route('profile.updateDescription') }}" method="POST">
                                    @csrf
                                    @method('PUT')
                                    <textarea name="description" rows="4" class="w-full">{{ $user->description ?? '' }}</textarea>
                                    <button type="submit" class="profile-settings-btn">Save</button>
                                </form>
                            @else
                                <p>{{ $user->description ?? 'No description available.' }}</p>
                                <form action="{{ route('profile.startEditing') }}" method="POST">
                                    @csrf
                                    <button type="submit" class="profile-edit-icon">✎</button>
                                </form>
                            @endif
                        @else
                            <p>{{ $user->description ?? 'No description available.' }}</p>
                        @endif
                    </div>

                    <!-- Пости користувача / Лайкнуті пости -->
                    <div class="profile-posts">
                        <div class="flex items-center gap-3">
                            <h3 id="posts-title">USER POSTS:</h3>
                            <button id="toggle-posts-btn" class="profile-toggle-button">❤️</button>
                        </div>

                        <!-- Пости користувача -->
                        <div id="user-posts" class="user-posts">
                            @if(auth()->id() === $user->id)
                                <div class="post-card add-post-card">
                                    <a class="add-post gallery-image" href="{{ route('create.post') }}">+</a>
                                </div>
                            @endif

                            @forelse ($userPosts as $post)
                                <div class="post-card">
                                    <a href="{{ route('artwork.show', $post->slug) }}">
                                        <img src="{{ Str::startsWith($post->thumbnail, 'http') ? $post->thumbnail : asset($post->thumbnail) }}" alt="{{ $post->meta_title }}" class="gallery-image" loading="lazy">
                                    </a>
                                </div>
                            @empty
                                <p>No posts available.</p>
                            @endforelse
                        </div>

                        <!-- Лайкнуті пости -->
                        <div id="liked-posts" class="user-posts" style="display: none;">
                            @forelse($likedPosts as $post)
                                <div class="post-card">
                                    <a href="{{ route('artwork.show', $post->slug) }}">
                                        <img src="{{ Str::startsWith($post->thumbnail, 'http') ? $post->thumbnail : asset($post->thumbnail) }}" alt="{{ $post->meta_title }}" class="gallery-image" loading="lazy">
                                    </a>
                                </div>
                            @empty
                                <p>No liked posts yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.getElementById('toggle-posts-btn').addEventListener('click', function(event) {
            event.preventDefault();

            const userPosts = document.getElementById('user-posts');
            const likedPosts = document.getElementById('liked-posts');

            if (likedPosts.style.display === 'none' || likedPosts.style.display === '') {
                likedPosts.style.display = 'flex';
                userPosts.style.display = 'none';
                this.textContent = '👤';
            } else {
                likedPosts.style.display = 'none';
                userPosts.style.display = 'flex';
                this.textContent = '❤️';
            }
        });
    </script>
</x-app-layout>
