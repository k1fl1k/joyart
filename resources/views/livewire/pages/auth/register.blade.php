<?php

use k1fl1k\joyart\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Livewire\Attributes\Layout;
use Livewire\Volt\Component;

new #[Layout('layouts.guest')] class extends Component
{
    public string $username = '';
    public string $email = '';
    public string $password = '';
    public string $password_confirmation = '';

    /**
     * Handle an incoming registration request.
     */
    public function register(): void
    {
        $validated = $this->validate([
            'username' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ]);

        $validated['password'] = Hash::make($validated['password']);

        event(new Registered($user = User::create($validated)));

        Auth::login($user);

        $this->redirect(route('verification.notice'), navigate: true);
    }
}; ?>

<div>
    <form wire:submit="register">
        <div> <!-- This will be the single root element -->

                    <form wire:submit="register" class="mt-6">
                        <!-- Name -->
                        <div class="mb-4">
                            <label for="username" class="register-form-label">Username</label>
                            <input wire:model="username" id="username" type="text"
                                   class="register-form-input"
                                   required autofocus autocomplete="username">
                            <span class="register-form-error">
                        @error('username') {{ $message }} @enderror
                    </span>
                        </div>

                        <!-- Email -->
                        <div class="mb-4">
                            <label for="email" class="register-form-label">Email</label>
                            <input wire:model="email" id="email" type="email"
                                   class="register-form-input"
                                   required autocomplete="username">
                            <span class="register-form-error">
                        @error('email') {{ $message }} @enderror
                    </span>
                        </div>

                        <!-- Password -->
                        <div class="mb-4">
                            <label for="password" class="register-form-label">Password</label>
                            <input wire:model="password" id="password" type="password"
                                   class="register-form-input"
                                   required autocomplete="new-password">
                            <span class="register-form-error">
                        @error('password') {{ $message }} @enderror
                    </span>
                        </div>

                        <!-- Confirm Password -->
                        <div class="mb-4">
                            <label for="password_confirmation" class="register-form-label">Confirm Password</label>
                            <input wire:model="password_confirmation" id="password_confirmation" type="password"
                                   class="register-form-input"
                                   required autocomplete="new-password">
                            <span class="register-form-error">
                        @error('password_confirmation') {{ $message }} @enderror
                    </span>
                        </div>

                        <div class="flex justify-between items-center">
                            <button type="submit" class="register-form-button">
                                Register
                            </button>
                        </div>
                    </form>

        </div> <!-- End of the single root element -->

    </form>
</div>
