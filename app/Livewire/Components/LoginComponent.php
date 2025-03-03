<?php

namespace k1fl1k\joyart\Livewire\Components;

use k1fl1k\joyart\Livewire\Forms\LoginForm;
use Livewire\Component;

class LoginComponent extends Component
{
    public LoginForm $form;

    public function mount()
    {
        $this->form = new LoginForm();
    }

    public function login()
    {
        $this->form->validate();
        $this->form->authenticate();

        session()->regenerate();

        return redirect()->intended(route('dashboard'));
    }
}
