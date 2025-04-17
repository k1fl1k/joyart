<div class="mb-6">
    @if (session()->has('message'))
        <div class="text-green-500 mb-2">{{ session('message') }}</div>
    @endif

    <form wire:submit.prevent="save" class="space-y-4" enctype="multipart/form-data">
        <div>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
                {{__('Change avatar')}}
            </h2>
            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                {{ __('Once your avatar is changed, you can`t undo that..') }}
            </p>

            <!-- Input for file upload -->
            <input type="file" wire:model="avatar" class="w-full text-white" id="avatar">

            @error('avatar') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

            <div wire:loading wire:target="avatar" class="text-white text-sm mt-1">
                Завантаження...
            </div>

            <!-- Preview Image Section -->
            @if ($previewImage)
                <div class="mt-2">
                    <img src="{{ $previewImage }}" alt="Прев'ю" class="w-24 h-24 rounded-full object-cover">
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Оновити
            </x-primary-button>
        </div>
    </form>
</div>

<script>
    function previewImage(event) {
        const file = event.target.files[0];
        const previewContainer = document.getElementById('image-preview-container');
        const previewImage = document.getElementById('image-preview');

        // Check if file is an image
        if (file && file.type.startsWith('image/')) {
            const reader = new FileReader();

            reader.onload = function(e) {
                // Set image source to the loaded file
                previewImage.src = e.target.result;
                previewContainer.style.display = 'block'; // Show the preview container
            };

            reader.readAsDataURL(file);
        } else {
            previewContainer.style.display = 'none'; // Hide the preview container if not an image
        }
    }
</script>
