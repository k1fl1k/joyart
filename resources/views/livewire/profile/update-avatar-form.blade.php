<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Аватар профілю
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Оновіть аватар вашого профілю.
        </p>
    </header>

    <div id="avatar-message" class="hidden mb-2"></div>

    <form id="avatar-form" class="mt-6 space-y-6" enctype="multipart/form-data">
        @csrf
        <div>
            <div class="flex items-center space-x-6">
                <div class="shrink-0">
                    <img id="avatar-preview" class="h-16 w-16 object-cover rounded-full"
                         src="{{ auth()->user()->avatar ? auth()->user()->avatar : asset('storage/images/avatar-male.png') }}"
                         alt="Аватар користувача">
                </div>
                <label class="block">
                    <span class="sr-only">Виберіть фото профілю</span>
                    <input type="file" name="avatar" id="avatar-input"
                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                           accept="image/*" />
                    <span id="avatar-error" class="text-red-500 text-sm hidden"></span>
                </label>
            </div>
        </div>
        <div class="flex items-center gap-4">
            <button type="submit" id="avatar-submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
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

        document.getElementById('avatar-form').addEventListener('submit', async function(event) {
            event.preventDefault();
            const form = event.target;
            const submitButton = document.getElementById('avatar-submit');
            const messageDiv = document.getElementById('avatar-message');
            const errorSpan = document.getElementById('avatar-error');

            submitButton.disabled = true;
            messageDiv.classList.add('hidden');
            errorSpan.classList.add('hidden');

            const formData = new FormData(form);

            try {
                const response = await fetch('{{ route('profile.updateAvatar') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: formData,
                });

                const data = await response.json();

                if (response.ok) {
                    messageDiv.classList.remove('hidden', 'text-red-500');
                    messageDiv.classList.add('text-green-500');
                    messageDiv.textContent = data.message;
                    document.getElementById('avatar-preview').src = data.avatar_url;
                } else {
                    errorSpan.classList.remove('hidden');
                    errorSpan.textContent = data.error || 'Помилка при завантаженні аватара';
                }
            } catch (error) {
                errorSpan.classList.remove('hidden');
                errorSpan.textContent = 'Виникла помилка: ' + error.message;
            } finally {
                submitButton.disabled = false;
            }
        });
    </script>
</section>
