<div class="mb-6">
    @if (session()->has('message'))
        <div class="text-green-500 mb-2">{{ session('message') }}</div>
    @endif

    @if (session()->has('error'))
        <div class="text-red-500 mb-2">{{ session('error') }}</div>
    @endif

    @if (session()->has('warning'))
        <div class="text-yellow-500 mb-2">{{ session('warning') }}</div>
    @endif

    @if ($uploadError)
        <div class="text-red-500 mb-2">{{ $uploadError }}</div>
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
            <div class="mt-2">
                <label for="avatar" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Виберіть файл зображення</label>
                <input type="file" wire:model="avatar" class="w-full text-white rounded-md p-2" id="avatar" accept="image/jpeg,image/png,image/gif">
                <p class="text-xs text-gray-500 mt-1">Підтримувані формати: JPG, PNG, GIF. Максимальний розмір: 2MB</p>
            </div>

            @error('avatar') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror

            <div wire:loading wire:target="avatar" class="text-white text-sm mt-1">
                Завантаження...
            </div>

            <!-- Preview Image Section -->
            <div class="mt-2">
                @if ($previewImage)
                    <img src="{{ $previewImage }}" alt="Прев'ю" class="w-24 h-24 rounded-full object-cover">
                @elseif (Auth::user()->avatar)
                    <img src="{{ Auth::user()->avatar }}" alt="Поточний аватар" class="w-24 h-24 rounded-full object-cover">
                    <p class="text-xs text-gray-500 mt-1">Поточний аватар</p>
                @else
                    <img src="{{ asset('storage/images/avatar-male.png') }}" alt="Заглушка" class="w-24 h-24 rounded-full object-cover">
                @endif
            </div>
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Оновити
            </x-primary-button>

            <button type="button" wire:click="save" class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                Завантажити вручну
            </button>
        </div>
    </form>
</div>

<!-- Видаляємо скрипт, оскільки використовуємо Livewire для попереднього перегляду -->
