<?php

namespace k1fl1k\joyart\Livewire\Forms;

use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use k1fl1k\joyart\Enums\Role;
use k1fl1k\joyart\View\User;
use Livewire\Attributes\Validate;
use Livewire\Form;

class RegisterForm extends Form
{
    #[Validate('required|string|min:3|max:255|unique:users,username')]
    public string $username = '';

    #[Validate('required|email|unique:users,email')]
    public string $email = '';

    public function rules()
    {
        return [
            'password' => ['required', Password::defaults()],
            'password_confirmation' => ['required', 'same:password'],
        ];
    }

    public string $password = '';

    public string $password_confirmation = '';

    #[Validate('nullable|date')]
    public ?string $birthday = null;

    #[Validate('nullable|in:male,female,other')]
    public ?string $gender = null;

    #[Validate('nullable|boolean')]
    public bool $allow_adult = false;

    public function register()
    {
        $this->validate();

        $user = User::create([
            'id' => (string) Str::ulid(), // Генеруємо ULID
            'username' => $this->username,
            'email' => $this->email,
            'password' => Hash::make($this->password),
            'birthday' => $this->birthday,
            'gender' => $this->gender,
            'role' => Role::USER->value, // За замовчуванням роль USER
            'allow_adult' => $this->allow_adult,
        ]);

        auth()->login($user);

        return redirect()->route('dashboard'); // Перенаправлення після реєстрації
    }
}
