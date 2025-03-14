<x-app-layout>
    <div class="py-12">
        <div class="profile-container">
            <div class="profile-info">
                <!-- Аватар і ім'я -->
                <div class="profile-header">
                    <div class="profile-circle">
                        <img src="{{ auth()->user()->avatar ?? asset('storage/images/avatar-male.png') }}"
                             alt="User Avatar">
                    </div>
                    <div>
                        <h2>{{ auth()->user()->username }}</h2>
                        <p class="text-gray-400">role: {{ auth()->user()->role }}</p>
                    </div>
                </div>

                <!-- Блоки інформації -->
                <div class="profile-content">
                    <!-- Опис користувача -->
                    <div class="profile-description">
                        <h3>Description</h3>
                        <p>{{ auth()->user()->description ?? 'No description available.' }}</p>
                        <a class="profile-edit-icon">✎</a>
                    </div>

                    <!-- Пости користувача / Лайкнуті пости -->
                    <div class="profile-posts">
                        <div class="flex items-center gap-3">
                            <h3 id="posts-title">USER POSTS:</h3>
                            <button id="toggle-button" class="profile-toggle-button" data-current="user">
                                👤
                            </button>
                        </div>

                        <!-- Пости користувача -->
                        <div id="user-posts">
                            @forelse($userPosts as $post)
                                <div class="post-card">
                                    <img src="{{ asset($post->thumbnail) }}" alt="meta_title">
                                </div>
                            @empty
                                <p>No posts yet.</p>

                            @endforelse
                        </div>

                        <!-- Лайкнуті пости -->
                        <div id="liked-posts" style="display: none;">
                            @forelse($likedPosts as $post)
                                <div class="post-card">
                                    <img src="{{ asset($post->thumbnail) }}" alt="Post Thumbnail">
                                    <p>{{ $post->meta_title }}</p>
                                </div>
                            @empty
                                <p>No liked posts yet.</p>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Кнопка налаштувань -->
                <div class="mt-6 flex justify-end">
                    <a href="{{ route('settings') }}" class="profile-settings-btn">
                        Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
