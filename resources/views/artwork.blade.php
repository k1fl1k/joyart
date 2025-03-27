<x-app-layout>
    <div class="content">
        <x-tags-sidebar :tags="$tags" />
        <div class="main">
            <div class="gallery">
                <div class="profile-header">
                    <div class="profile-circle">
                        <img src="{{ $user->avatar ?? asset('storage/images/avatar-male.png') }}"
                             alt="{{ $user->username }}'s Avatar">
                    </div>
                    <div>
                        <h2>{{ $user->username }}</h2>
                        <p class="text-gray-400">Role: {{ $user->role }}</p>
                    </div>
                </div>
                @php
                    $colors = json_decode($artwork->colors, true) ?? [];
                @endphp
{{--                <div class="full-artwork"--}}
{{--                     style="box-shadow: 0 4px 20px {{ $colors[0] ?? 'transparent' }};">--}}
{{--                    <img src="{{ $artwork->original }}"--}}
{{--                         alt="{{ $artwork->image_alt }}"--}}
{{--                         loading="lazy"--}}
{{--                         class="artwork-image" />--}}
{{--                </div>--}}
                @if(isset($artwork))
                    <div class="full-artwork"
                         style="box-shadow: 0 -10px 20px {{ $colors[0] ?? 'transparent' }},
                0 10px 20px {{ $colors[1] ?? 'transparent' }};">
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
                                 class="artwork-image" />
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
</x-app-layout>
