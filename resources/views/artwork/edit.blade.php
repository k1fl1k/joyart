<x-app-layout>
    <div class="create-post-container flex">
        <!-- Панель зліва -->
        <div class="w-1/3 p-4 border-r">
            <h2 class="text-lg font-bold mb-2">Preview</h2>
            <div class="border rounded-lg p-2 bg-gray-100 flex justify-center items-center relative cursor-pointer" onclick="document.getElementById('original').click();">
                <input type="file" name="original" id="original" class="hidden" accept="image/*,video/*">

                @if ($artwork->file_ext === 'mp4' || $artwork->file_ext === 'webm')
                    <video id="videoPreview" class="w-full h-auto rounded-md" controls>
                        <source src="{{ asset($artwork->original) }}" type="video/{{ $artwork->file_ext }}">
                    </video>
                    <img id="imagePreview" class="w-full h-auto rounded-md hidden" alt="Image preview">
                @else
                    <img id="imagePreview" class="w-full h-auto rounded-md" src="{{ asset($artwork->original) }}" alt="Image preview">
                    <video id="videoPreview" class="w-full h-auto rounded-md hidden" controls></video>
                @endif

                <p id="noMediaText" class="text-gray-500 text-sm hidden">Click to upload an image or video</p>
            </div>
        </div>

        <!-- Форма справа -->
        <div class="w-2/3 p-4">
            <h1 class="register-form-header">Edit Artwork</h1>
            <form action="{{ route('artworks.update', $artwork->slug) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="register-form-group">
                    <label for="meta_title" class="register-form-label">Title</label>
                    <input type="text" name="meta_title" id="meta_title" class="register-form-input" value="{{ old('meta_title', $artwork->meta_title) }}">
                </div>

                <div class="register-form-group">
                    <label for="meta_description" class="register-form-label">Description</label>
                    <textarea name="meta_description" id="meta_description" class="register-form-input">{{ old('meta_description', $artwork->meta_description) }}</textarea>
                </div>

                <div class="register-form-group">
                    <label for="image_alt" class="register-form-label">Alt name</label>
                    <input type="text" name="image_alt" id="image_alt" class="register-form-input" value="{{ old('image_alt', $artwork->image_alt) }}">
                </div>

                <!-- Чекбокс "Опублікований пост" -->
                <div class="register-form-group flex items-center">
                    <input type="checkbox" name="is_published" id="is_published" class="mr-2" {{ $artwork->is_published ? 'checked' : '' }}>
                    <label for="is_published" class="register-form-label">Published</label>
                </div>

                <!-- Теги -->
                <div class="register-form-group relative">
                    <label for="tags" class="register-form-label">Tags</label>
                    <div class="border p-2 rounded-lg">
                        <div id="selected-tags" class="flex flex-wrap gap-2 mb-2">
                            @foreach ($artwork->tags as $tag)
                                <span class="bg-blue-500 text-white text-sm px-2 py-1 rounded flex items-center">
                                    {{ $tag->name }}
                                    <button type="button" class="ml-2 text-white remove-tag" data-tag="{{ $tag->name }}">x</button>
                                </span>
                            @endforeach
                        </div>
                        <input type="text" id="tag-input" class="register-form-input" placeholder="Type a tag and press Enter">
                        <input type="hidden" name="tags" id="hidden-tags" value="{{ implode(',', $artwork->tags->pluck('name')->toArray()) }}">
                        <div id="tag-suggestions" class="tag-suggestions hidden"></div>
                    </div>
                </div>

                <button type="submit" class="register-form-button">Save Changes</button>
            </form>
        </div>
    </div>

    <script>
        document.getElementById('original').addEventListener('change', function(event) {
            const file = event.target.files[0];
            const imagePreview = document.getElementById('imagePreview');
            const videoPreview = document.getElementById('videoPreview');
            const noMediaText = document.getElementById('noMediaText');

            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    if (file.type.startsWith('image/')) {
                        imagePreview.src = e.target.result;
                        imagePreview.classList.remove('hidden');
                        videoPreview.classList.add('hidden');
                    } else if (file.type.startsWith('video/')) {
                        videoPreview.src = e.target.result;
                        videoPreview.classList.remove('hidden');
                        imagePreview.classList.add('hidden');
                    }
                    noMediaText.classList.add('hidden');
                };
                reader.readAsDataURL(file);
            }
        });

        document.addEventListener('DOMContentLoaded', function() {
            const tagInput = document.getElementById('tag-input');
            const tagContainer = document.getElementById('selected-tags');
            const hiddenTags = document.getElementById('hidden-tags');
            const suggestionsContainer = document.getElementById('tag-suggestions');

            let selectedTags = hiddenTags.value.split(',').filter(t => t.trim() !== '');

            function updateHiddenInput() {
                hiddenTags.value = selectedTags.join(',');
            }

            function addTag(tag) {
                if (!selectedTags.includes(tag)) {
                    selectedTags.push(tag);
                    updateHiddenInput();

                    const tagElement = document.createElement('span');
                    tagElement.className = 'bg-blue-500 text-white text-sm px-2 py-1 rounded flex items-center';
                    tagElement.innerHTML = `${tag} <button type="button" class="ml-2 text-white remove-tag" data-tag="${tag}">x</button>`;

                    tagContainer.appendChild(tagElement);
                }
                tagInput.value = '';
                suggestionsContainer.classList.add('hidden');
            }

            tagInput.addEventListener('keydown', function(event) {
                if (event.key === 'Enter') {
                    event.preventDefault();
                    const tag = tagInput.value.trim();
                    if (tag !== '') {
                        addTag(tag);
                    }
                }
            });

            tagContainer.addEventListener('click', function(event) {
                if (event.target.classList.contains('remove-tag')) {
                    const tag = event.target.getAttribute('data-tag');
                    selectedTags = selectedTags.filter(t => t !== tag);
                    updateHiddenInput();
                    event.target.parentElement.remove();
                }
            });

            tagInput.addEventListener('input', async function() {
                const query = tagInput.value.trim();
                if (query.length > 1) {
                    const response = await fetch(`/tags/search?query=${query}`);
                    const tags = await response.json();

                    suggestionsContainer.innerHTML = '';
                    suggestionsContainer.classList.remove('hidden');

                    tags.forEach(tag => {
                        const suggestion = document.createElement('div');
                        suggestion.className = 'p-2 hover:bg-gray-200 cursor-pointer';
                        suggestion.innerText = tag.name;
                        suggestion.addEventListener('click', function() {
                            addTag(tag.name);
                        });
                        suggestionsContainer.appendChild(suggestion);
                    });
                } else {
                    suggestionsContainer.classList.add('hidden');
                }
            });
        });
    </script>
</x-app-layout>
