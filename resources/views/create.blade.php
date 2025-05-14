<x-app-layout>
    <div class="create-post-container flex">
        <!-- Панель зліва -->
        <div class="w-1/3 p-4 border-r">
            <h2 class="text-lg font-bold mb-2">Preview</h2>
            <div class="border rounded-lg p-2 bg-gray-100 flex justify-center items-center relative cursor-pointer">
                <img id="imagePreview" class="w-full h-auto rounded-md hidden" alt="Image preview">
                <video id="videoPreview" class="w-full h-auto rounded-md hidden" controls></video>
                <p id="noMediaText" class="text-gray-500 text-sm">Click to upload an image or video</p>
            </div>
        </div>

        <!-- Форма справа -->
        <div class="w-2/3 p-4">
            <h1 class="register-form-header">Create Post</h1>
            @if ($errors->any())
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <ul>
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
            <form action="{{ route('artworks.store') }}" method="POST" enctype="multipart/form-data" id="artwork-form">
                @csrf
                <!-- File input is now visible for easier debugging -->
                <div class="mb-4 p-2 border rounded">
                    <label for="original" class="block text-sm font-medium mb-1">Upload File (Required)</label>
                    <input type="file" name="original" id="original" class="w-full" accept="image/*,video/*">
                    <p class="text-xs text-gray-500 mt-1">Supported formats: JPG, JPEG, PNG, GIF, MP4, MOV, AVI</p>
                </div>

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
                    <label for="meta_title" class="register-form-label">Title</label>
                    <input type="text" name="meta_title" id="meta_title" class="register-form-input">
                </div>

                <div class="register-form-group">
                    <label for="meta_description" class="register-form-label">Description</label>
                    <textarea name="meta_description" id="meta_description" class="register-form-input"></textarea>
                </div>

                <div class="register-form-group">
                    <label for="image_alt" class="register-form-label">Alt name</label>
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


        // File change event listener moved to the main form handling code


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

            const form = document.getElementById("artwork-form");
            const fileInput = document.getElementById("original");
            const previewImage = document.getElementById("imagePreview");
            const previewVideo = document.getElementById("videoPreview");
            const noMediaText = document.getElementById("noMediaText");

            // Function to update the preview when a file is selected
            function updatePreview() {
                if (fileInput.files && fileInput.files[0]) {
                    const file = fileInput.files[0];
                    const reader = new FileReader();

                    reader.onload = function(e) {
                        if (file.type.startsWith('image/')) {
                            previewImage.src = e.target.result;
                            previewImage.classList.remove('hidden');
                            previewVideo.classList.add('hidden');
                        } else if (file.type.startsWith('video/')) {
                            previewVideo.src = e.target.result;
                            previewVideo.classList.remove('hidden');
                            previewImage.classList.add('hidden');
                        }
                        noMediaText.classList.add('hidden');
                    };

                    reader.readAsDataURL(file);
                }
            }

            // Update preview when file is selected
            fileInput.addEventListener("change", updatePreview);

            // Handle form submission
            form.addEventListener("submit", function (event) {
                // Check if file is selected
                if (!fileInput.files.length) {
                    event.preventDefault();
                    alert("Please select a file before submitting!");
                    return;
                }

                // On Azure, we'll use traditional form submission by default
                // as it's more reliable for file uploads
                if (window.location.hostname.includes('azure') ||
                    window.location.hostname.includes('.com') ||
                    window.location.hostname.includes('.net')) {
                    console.log("Using traditional form submission on production server");
                    return true; // Allow normal form submission
                }

                // For local development, try fetch API first with fallback
                try {
                    event.preventDefault();

                    const formData = new FormData(form);

                    // Log what we're sending
                    console.log("Submitting form with file:", fileInput.files[0].name);

                    // Show loading indicator
                    const submitBtn = form.querySelector('button[type="submit"]');
                    const originalText = submitBtn.textContent;
                    submitBtn.textContent = "Uploading...";
                    submitBtn.disabled = true;

                    fetch(form.action, {
                        method: "POST",
                        body: formData,
                    })
                    .then((res) => {
                        if (res.redirected) {
                            window.location.href = res.url;
                            return { success: true };
                        }
                        if (!res.ok) {
                            throw new Error(`Server responded with ${res.status}: ${res.statusText}`);
                        }
                        return res.json();
                    })
                    .then((data) => {
                        if (data.success) {
                            window.location.href = "{{ route('welcome') }}";
                        } else {
                            alert("Error uploading file.");
                            submitBtn.textContent = originalText;
                            submitBtn.disabled = false;
                        }
                    })
                    .catch((err) => {
                        console.error("Fetch error:", err);
                        alert("Using traditional form submission as fallback. Please wait...");
                        // Submit the form traditionally
                        form.removeEventListener('submit', arguments.callee);
                        form.submit();
                    });
                } catch (error) {
                    console.error("Error in fetch submission, falling back to traditional form submission", error);
                    // Allow the form to submit normally
                    return true;
                }
            });
        });
    </script>
</x-app-layout>
