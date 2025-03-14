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
