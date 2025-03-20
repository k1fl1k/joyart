@vite(['resources/css/app.css'])
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

                    <div class="full-artwork">
                        <img src="{{ $artwork->original }}" alt="{{ $artwork->image_alt }}" loading="lazy" class="artwork-image" />
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
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
