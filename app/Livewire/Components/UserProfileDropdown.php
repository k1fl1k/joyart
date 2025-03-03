<?php

namespace k1fl1k\joyart\Livewire\Components;

use k1fl1k\joyart\Livewire\Actions\Logout;
use Livewire\Component;

class UserProfileDropdown extends Component
{
    public $user;

    public function mount()
    {
        $this->user = auth()->user();
    }

    /**
     * Log the current user out of the application.
     */
    public function logout(Logout $logout)
    {
        $logout();

        return redirect('/');  // Перенаправлення після виходу
    }

    public function render()
    {
        return view('livewire.user-profile-dropdown');
    }
}
