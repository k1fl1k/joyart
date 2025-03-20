<div class="sidebar">
    @foreach ($tags as $tag)
        <div class="tag">
            <a href="{{ route('gallery.byTag', $tag->slug) }}" class="tag-link">
                {{ $tag->name }}
            </a>

            @if ($tag->subtags->isNotEmpty())
                <div class="subtags">
                    @foreach ($tag->subtags as $subtag)
                        <a href="{{ route('gallery.byTag', $subtag->slug) }}" class="subtag">
                            {{ $subtag->name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>
    @endforeach
</div>
