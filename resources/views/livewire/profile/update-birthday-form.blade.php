<div class="mb-6">
    @if (session()->has('message'))
        <div class="text-green-500 mb-2">{{ session('message') }}</div>
    @endif

    <form wire:submit.prevent="updateBirthday" class="space-y-4">
        <div>
            <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">Дата народження</h2>
            <input type="date" wire:model="birthday" class="w-full rounded p-2 text-black">
            @error('birthday') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
        </div>

        <div>
            <label class="inline-flex items-center">
                <input type="checkbox" wire:model="allow_adult" class="rounded text-blue-600"
                    @disabled(!$isOldEnough)>
                <span class="ml-2 text-sm text-gray-700 dark:text-gray-300">Дозволити контент 16+</span>
            </label>
            @if(!$isOldEnough)
                <p class="text-xs text-red-400 mt-1">Ця опція доступна лише після досягнення 16 років.</p>
            @endif
        </div>

        <x-primary-button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
            Оновити
        </x-primary-button>
    </form>
</div>
