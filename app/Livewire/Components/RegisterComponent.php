<?php

namespace k1fl1k\joyart\Livewire\Components;

use k1fl1k\joyart\Livewire\Forms\RegisterForm;
use Livewire\Component;

class RegisterComponent extends Component
{
    public RegisterForm $form;

    public function render()
    {
        return view('livewire.register');
    }

    public function submit()
    {
        $this->form->register();
    }
}
