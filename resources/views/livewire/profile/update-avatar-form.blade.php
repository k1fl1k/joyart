<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Аватар профілю
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Оновіть аватар вашого профілю.
        </p>
    </header>

    @if (session('message'))
        <div class="text-green-500 mb-2">{{ session('message') }}</div>
    @endif

    @if (session('error'))
        <div class="text-red-500 mb-2">{{ session('error') }}</div>
    @endif

    <form method="post" action="{{ route('profile.updateAvatar') }}" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf

        <div>
            <div class="flex items-center space-x-6">
                <div class="shrink-0">
                    <img id="avatar-preview" class="h-16 w-16 object-cover rounded-full"
                         src="{{ auth()->user()->avatar ? auth()->user()->avatar : 'storage/images/avatar-male.png' }}"
                         alt="Аватар користувача">
                </div>
                <label class="block">
                    <span class="sr-only">Виберіть фото профілю</span>
                    <input type="file" name="avatar" id="avatar-input"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                           accept="image/*" />
                    @error('avatar')
                    <span class="text-red-500 text-sm">{{ $message }}</span>
                    @enderror
                </label>
            </div>
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Оновити аватар
            </button>
        </div>
    </form>

    <script>
        document.getElementById('avatar-input').addEventListener('change', function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('avatar-preview').src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</section>
