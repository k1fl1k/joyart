<x-app-layout>
    <div class="create-post-container flex">
        <!-- Панель зліва -->
        <div class="w-1/3 p-4 border-r">
            <h2 class="text-lg font-bold mb-2">Preview</h2>
            <div class="border rounded-lg p-2 bg-gray-100 flex justify-center items-center relative cursor-pointer" onclick="document.getElementById('original').click();">
                <input type="file" name="original" id="original" class="hidden" accept="image/*,video/*">
                <img id="imagePreview" class="w-full h-auto rounded-md hidden" alt="Image preview">
                <video id="videoPreview" class="w-full h-auto rounded-md hidden" controls></video>
                <p id="noMediaText" class="text-gray-500 text-sm">Click to upload an image or video</p>
            </div>
        </div>

        <!-- Форма справа -->
        <div class="w-2/3 p-4">
            <h1 class="register-form-header">Create Post</h1>
            <form action="{{ route('artworks.store') }}" method="POST" enctype="multipart/form-data">
                @csrf

                <div class="register-form-group">
                    <label for="type" class="register-form-label">Type</label>
                    <select name="type" id="type" class="register-form-input">
                        <option value="image">Image</option>
                        <option value="animation">Animation</option>
                        <option value="video">Video</option>
                    </select>
                </div>

                <div class="register-form-group">
                    <label for="rating" class="register-form-label">Rating</label>
                    <select name="rating" id="rating" class="register-form-input">
                        <option value="general">General</option>
                        <option value="sensitive">Sensitive</option>
                        <option value="questionable">Questionable</option>
                    </select>
                </div>

                <div class="register-form-group">
                    <label for="is_vip" class="register-form-label">Is VIP</label>
                    <select name="is_vip" id="is_vip" class="register-form-input">
                        <option value="0">No</option>
                        <option value="1">Yes</option>
                    </select>
                </div>

                <div class="register-form-group">
                    <label for="meta_title" class="register-form-label">Meta Title</label>
                    <input type="text" name="meta_title" id="meta_title" class="register-form-input">
                </div>

                <div class="register-form-group">
                    <label for="meta_description" class="register-form-label">Meta Description</label>
                    <textarea name="meta_description" id="meta_description" class="register-form-input"></textarea>
                </div>

                <div class="register-form-group">
                    <label for="image_alt" class="register-form-label">Image Alt</label>
                    <input type="text" name="image_alt" id="image_alt" class="register-form-input">
                </div>

                <div class="register-form-group relative">
                    <label for="tags" class="register-form-label">Tags</label>
                    <div class="border p-2 rounded-lg">
                        <div id="selected-tags" class="flex flex-wrap gap-2 mb-2"></div>
                        <input type="text" id="tag-input" class="register-form-input" placeholder="Type a tag and press Enter">
                        <input type="hidden" name="tags" id="hidden-tags">
                        <div id="tag-suggestions" class="tag-suggestions"></div>
                    </div>
                </div>

                <button type="submit" class="register-form-button">Create Post</button>
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

            let selectedTags = [];

            function updateHiddenInput() {
                hiddenTags.value = selectedTags.join(',');
            }

            function addTag(tag) {
                if (!selectedTags.includes(tag)) {
                    selectedTags.push(tag);
                    updateHiddenInput();

                    const tagElement = document.createElement('span');
                    tagElement.className = 'bg-blue-500 text-white text-sm px-2 py-1 rounded flex items-center';
                    tagElement.innerHTML = `${tag} <button type="button" class="ml-2 text-white" onclick="removeTag('${tag}', this)">x</button>`;

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

            window.removeTag = function(tag, element) {
                selectedTags = selectedTags.filter(t => t !== tag);
                updateHiddenInput();
                element.parentElement.remove();
            };

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

            const form = document.querySelector("form");
            const fileInput = document.getElementById("original");

            form.addEventListener("submit", function (event) {
                if (!fileInput.files.length) {
                    alert("Please select a file before submitting!");
                    event.preventDefault();
                    return;
                }

                const formData = new FormData(form);
                formData.append("original", fileInput.files[0]);

                // Відправляємо форму вручну через Fetch API (альтернатива)
                fetch(form.action, {
                    method: "POST",
                    body: formData,
                    headers: {
                        "X-CSRF-TOKEN": document.querySelector('input[name="_token"]').value,
                    },
                })
                .then((response) => response.json())
                .then((data) => {
                    if (data.success) {
                        window.location.href = "{{ route('welcome') }}"; // Перенаправлення після успіху
                    } else {
                        alert("Error uploading file.");
                    }
                })
                .catch((error) => {
                    console.error("Upload error:", error);
                });

                event.preventDefault(); // Запобігаємо стандартному сабміту
            });
        });
    </script>
</x-app-layout>
