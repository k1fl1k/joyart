<?php

namespace k1fl1k\joyart\App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class UpdateAvatarForm extends Component
{
    use WithFileUploads;

    public $avatar;
    public $previewImage = null;

    public function updatedAvatar()
    {
        $this->validate([
            'avatar' => 'image|max:2048',
        ]);

        // Генерація попереднього перегляду зображення
        $this->previewImage = $this->avatar->temporaryUrl();
    }

    public function save()
    {
        $this->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        $user = Auth::user();

        // Delete the old avatar if it exists
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }

        // Store the file to the public disk
        $path = $this->avatar->store('avatars', 'public');

        $fileUrl = Storage::url($path);

        // Save the new path to the database
        $user->avatar = $fileUrl;
        $user->save();

        session()->flash('message', 'Аватар оновлено!');
        $this->reset('avatar');
        $this->previewImage = null;
    }

    public function render()
    {
        return view('livewire.profile.update-avatar-form');
    }
}
