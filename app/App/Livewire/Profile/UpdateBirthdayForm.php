<?php

namespace k1fl1k\joyart\App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class UpdateBirthdayForm extends Component
{
    public $birthday;
    public $allow_adult = false;
    public $isOldEnough = false;

    public function mount()
    {
        $user = Auth::user();
        $this->birthday = $user->birthday;
        $this->allow_adult = $user->allow_adult;
        $this->checkAge();
    }

    public function updatedBirthday()
    {
        $this->checkAge();

        if (!$this->isOldEnough) {
            $this->allow_adult = false;
        }
    }

    private function checkAge()
    {
        if ($this->birthday) {
            $age = \Carbon\Carbon::parse($this->birthday)->age;
            $this->isOldEnough = $age >= 16;
        } else {
            $this->isOldEnough = false;
        }
    }

    public function updateBirthday()
    {
        $this->validate([
            'birthday' => 'required|date|before:today',
        ]);

        $this->checkAge();

        $user = Auth::user();
        $user->birthday = $this->birthday;
        $user->allow_adult = $this->isOldEnough ? $this->allow_adult : false;
        $user->save();

        session()->flash('message', 'Дата народження оновлена!');
    }

}
