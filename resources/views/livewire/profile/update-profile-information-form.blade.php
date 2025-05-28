<?php

use k1fl1k\joyart\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\Rule;
use Livewire\Volt\Component;

new class extends Component
{
    public string $name = '';
    public string $email = '';

    /**
     * Mount the component.
     */
    public function mount(): void
    {
        $this->name = Auth::user()->username;
        $this->email = Auth::user()->email;
    }

    /**
     * Update the profile information for the currently authenticated user.
     */
    public function updateProfileInformation(): void
    {
        $user = Auth::user();

        $validated = $this->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
        ]);

        $user->fill($validated);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        $this->dispatch('profile-updated', name: $user->name);
    }

    /**
     * Send an email verification notification to the current user.
     */
    public function sendVerification(): void
    {
        $user = Auth::user();

        if ($user->hasVerifiedEmail()) {
            $this->redirectIntended(default: route('dashboard', absolute: false));

            return;
        }

        $user->sendEmailVerificationNotification();

        Session::flash('status', 'verification-link-sent');
    }
}; ?>

<section>
    <header>
        <h2 class="text-lg font-medium text-gray-900 dark:text-gray-100">
            Інформація профілю
        </h2>
        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            Оновіть інформацію профілю вашого облікового запису та адресу електронної пошти.
        </p>
    </header>

    <div id="profile-message" class="hidden mb-2"></div>

    <form id="profile-form" class="mt-6 space-y-6">
        @csrf
        <div>
            <label for="username" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Ім’я користувача</label>
            <input id="username" name="username" type="text" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300" required autofocus autocomplete="username" value="{{ auth()->user()->username }}" />
            <span id="username-error" class="text-red-500 text-sm hidden"></span>
        </div>

        <div>
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Електронна пошта</label>
            <input id="email" name="email" type="email" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-800 dark:text-gray-300" required autocomplete="email" value="{{ auth()->user()->email }}" />
            <span id="email-error" class="text-red-500 text-sm hidden"></span>

            @if (auth()->user() instanceof \Illuminate\Contracts\Auth\MustVerifyEmail && ! auth()->user()->hasVerifiedEmail())
                <div>
                    <p class="text-sm mt-2 text-gray-800 dark:text-gray-200">
                        Ваша електронна адреса не підтверджена.
                        <button type="button" id="send-verification" class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                            Натисніть тут, щоб повторно надіслати лист для підтвердження.
                        </button>
                    </p>
                    <div id="verification-message" class="hidden mt-2 font-medium text-sm text-green-600 dark:text-green-400"></div>
                </div>
            @endif
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" id="profile-submit" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                Зберегти
            </button>
            <div id="profile-success" class="hidden text-green-600">Збережено.</div>
        </div>
    </form>

    <script>
        document.getElementById('profile-form').addEventListener('submit', async function(event) {
            event.preventDefault();
            const form = event.target;
            const submitButton = document.getElementById('profile-submit');
            const messageDiv = document.getElementById('profile-message');
            const successDiv = document.getElementById('profile-success');
            const usernameError = document.getElementById('username-error');
            const emailError = document.getElementById('email-error');

            submitButton.disabled = true;
            messageDiv.classList.add('hidden');
            successDiv.classList.add('hidden');
            usernameError.classList.add('hidden');
            emailError.classList.add('hidden');

            const formData = new FormData(form);

            try {
                const response = await fetch('{{ route('profile.updateProfile') }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json', // Ensure JSON response
                    },
                    body: formData,
                });

                const text = await response.text(); // Get raw response
                let data;
                try {
                    data = JSON.parse(text); // Attempt to parse as JSON
                } catch (e) {
                    console.error('Invalid JSON response:', text);
                    throw new Error('Сервер повернув невалідну відповідь');
                }

                if (response.ok) {
                    messageDiv.classList.remove('hidden', 'text-red-500');
                    messageDiv.classList.add('text-green-500');
                    messageDiv.textContent = data.message;
                    successDiv.classList.remove('hidden');
                } else {
                    if (data.errors) {
                        if (data.errors.username) {
                            usernameError.classList.remove('hidden');
                            usernameError.textContent = data.errors.username[0];
                        }
                        if (data.errors.email) {
                            emailError.classList.remove('hidden');
                            emailError.textContent = data.errors.email[0];
                        }
                    } else {
                        messageDiv.classList.remove('hidden', 'text-green-500');
                        messageDiv.classList.add('text-red-500');
                        messageDiv.textContent = data.error || 'Помилка при оновленні профілю';
                    }
                }
            } catch (error) {
                console.error('Error:', error);
                messageDiv.classList.remove('hidden', 'text-green-500');
                messageDiv.classList.add('text-red-500');
                messageDiv.textContent = 'Виникла помилка: ' + error.message;
            } finally {
                submitButton.disabled = false;
            }
        });

        const sendVerificationButton = document.getElementById('send-verification');
        if (sendVerificationButton) {
            sendVerificationButton.addEventListener('click', async function() {
                const messageDiv = document.getElementById('verification-message');
                messageDiv.classList.add('hidden');

                try {
                    const response = await fetch('{{ route('profile.sendVerification') }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json', // Ensure JSON response
                        },
                    });

                    const text = await response.text();
                    let data;
                    try {
                        data = JSON.parse(text);
                    } catch (e) {
                        console.error('Invalid JSON response:', text);
                        throw new Error('Сервер повернув невалідну відповідь');
                    }

                    if (response.ok) {
                        messageDiv.classList.remove('hidden');
                        messageDiv.textContent = data.message;
                    } else {
                        messageDiv.classList.remove('hidden');
                        messageDiv.classList.add('text-red-500');
                        messageDiv.textContent = data.error || 'Помилка при надсиланні листа для підтвердження';
                    }
                } catch (error) {
                    console.error('Error:', error);
                    messageDiv.classList.remove('hidden');
                    messageDiv.classList.add('text-red-500');
                    messageDiv.textContent = 'Виникла помилка: ' + error.message;
                }
            });
        }
    </script>
</section>
