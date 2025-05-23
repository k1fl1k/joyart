<x-app-layout>
    <div class="content">
        <x-tags-sidebar :tags="$tags" />
        <div class="main">
            <div class="gallery">
                <div class="profile-header flex items-center space-x-4">
                    <a href="{{ route('profile.show', ['username' => $user->username]) }}" class="flex items-center space-x-3">
                        <div class="profile-circle">
                            <img src="{{ $user->avatar ?? asset('storage/images/avatar-male.png') }}"
                                 alt="{{ $user->username }}'s Avatar" class="w-12 h-12 rounded-full">
                        </div>
                        <div>
                            <h2 class="text-lg font-semibold">{{ $user->username }}</h2>
                            <p class="text-gray-400 text-sm">Role: {{ $user->role }}</p>
                        </div>
                    </a>
                    @if (Auth::check() && Auth::id() === $artwork->user_id)
                        <a href="{{ route('artworks.edit', $artwork->slug) }}" class="edit-button">Edit</a>
                        <form action="{{ route('artworks.destroy', $artwork->slug) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="delete-button">Delete</button>
                        </form>
                    @endif
                </div>
                @php
                    $colors = json_decode($artwork->colors, true) ?? [];
                @endphp
                @if(isset($artwork))
                    <div class="full-artwork">
                        @php
                            $isVideo = isset($artwork->original) && preg_match('/\.(mp4|webm|ogg)$/i', $artwork->original);
                        @endphp

                        @if ($isVideo)
                            <video controls class="artwork-image">
                                <source src="{{ Str::startsWith($artwork->original, 'http') ? $artwork->original : asset($artwork->original) }}" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        @else
                            <img src="{{ Str::startsWith($artwork->original, 'http') ? $artwork->original : asset($artwork->original) }}"
                                 alt="{{ $artwork->image_alt }}"
                                 loading="lazy"
                                 class="artwork-image"
                                 style="box-shadow: 0 -10px 20px {{ $colors[0] ?? 'transparent' }},
                                 0 10px 20px {{ $colors[1] ?? 'transparent' }};"/>
                        @endif
                    </div>
                    <div class="artwork-details">
                        <p>Resolution: {{ $artwork->width }}x{{ $artwork->height }}</p>
                        <p>Rating: {{ ucfirst($artwork->rating) }}</p>
                        <p>Tags:
                            @foreach ($artwork->tags as $tag)
                                <a href="{{ route('gallery.byTag', $tag->slug) }}" class="tag-link">{{ $tag->name }}</a>
                            @endforeach
                        </p>
                    </div>
                @endif
                <div class="artwork-actions">
                    <div class="flex items-center space-x-4 mb-4">
                        <span class="likes-count">{{ $artwork->likes()->count() }} likes</span>
                        <form action="{{ route('likes.toggle', $artwork->slug) }}" method="POST">
                            @csrf
                            <button type="submit" class="like-button">
                                @if ($artwork->isLikedByUser(auth()->id()))
                                    Dislike
                                @else
                                    Like
                                @endif
                            </button>
                        </form>
                        <form action="{{ route('favorites.toggle', $artwork->slug) }}" method="POST">
                            @csrf
                            <button class="like-button">
                                @if ($artwork->isFavoritedByUser(auth()->id()))
                                    Unfavorite
                                @else
                                    Favorite
                                @endif
                            </button>
                        </form>
                        @if (Auth::check() && Auth::id() !== $artwork->user_id)
                            <button type="button" class="report-button" onclick="openReportModal()">Поскаржитись</button>
                        @endif
                    </div>

                    <div class="comments-section">
                        <h3 class="text-lg font-semibold mb-2">Comments</h3>
                        <form action="{{ route('comments.store', $artwork->slug) }}" method="POST" class="comment-form mt-4">
                            @csrf
                            <textarea name="body" placeholder="Add a comment" rows="3"></textarea>
                            <button type="submit" class="comment-submit-button">Submit Comment</button>
                        </form>

                        @foreach ($comments as $comment)
                            <div class="comment">
                                <div>
                                    <img class="profile-circle" src="{{ $comment->user->avatar ?? asset('storage/images/avatar-male.png') }}"
                                         alt="{{ $comment->user->username }}'s Avatar">
                                    <p class="comment-meta">{{ $comment->user->username }}</p>
                                </div>
                                <p>{{ $comment->body }}</p>
                                @if (Auth::id() === $comment->user_id || Auth::user()->isAdmin())
                                    <form action="{{ route('comments.destroy', ['artwork' => $artwork->slug, 'comment' => $comment->id]) }}" method="POST">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">Видалити</button>
                                    </form>
                                @endif

                            </div>
                        @endforeach

                        <!-- Відображення пагінації -->
                        <div class="mt-4">
                            {{ $comments->links() }}
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </div>
    <!-- Report Modal -->
    <div id="reportModal" class="modal" style="display: none; position: fixed; z-index: 1000; left: 0; top: 0; width: 100%; height: 100%; overflow: auto; background-color: rgba(0,0,0,0.4);">
        <div class="modal-content" style="background-color: #1c1c1c; margin: 15% auto; padding: 20px; border: 1px solid #888; width: 80%; max-width: 500px; border-radius: 8px;">
            <span class="close" onclick="closeReportModal()" style="color: #aaa; float: right; font-size: 28px; font-weight: bold; cursor: pointer;">&times;</span>
            <h2 style="margin-bottom: 20px;">Поскаржитись на пост</h2>

            <form action="{{ route('artworks.report', $artwork->slug) }}" method="POST">
                @csrf
                <div style="margin-bottom: 15px;">
                    <label for="reason" style="display: block; margin-bottom: 5px;">Причина скарги:</label>
                    <select name="reason" id="reason" required class="rounded-lg bg-gray-800">
                        <option value="inappropriate_content">Неприйнятний вміст</option>
                        <option value="copyright_violation">Порушення авторських прав</option>
                        <option value="offensive">Образливий вміст</option>
                        <option value="spam">Спам</option>
                        <option value="other">Інше</option>
                    </select>
                </div>

                <div style="margin-bottom: 15px;">
                    <label for="description" style="display: block; margin-bottom: 5px;">Опис проблеми (необов'язково):</label>
                    <textarea name="description" id="description" rows="4" style="background-color: #222627; width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;"></textarea>
                </div>

                <button type="submit" style="background-color: #4CAF50; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer;">Надіслати скаргу</button>
                <button type="button" onclick="closeReportModal()" style="background-color: #f44336; color: white; padding: 10px 15px; border: none; border-radius: 4px; cursor: pointer; margin-left: 10px;">Скасувати</button>
            </form>
        </div>
    </div>

    <script>
        function openReportModal() {
            document.getElementById('reportModal').style.display = 'block';
        }

        function closeReportModal() {
            document.getElementById('reportModal').style.display = 'none';
        }

        // Close modal when clicking outside of it
        window.onclick = function(event) {
            const modal = document.getElementById('reportModal');
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        }
    </script>
</x-app-layout>
