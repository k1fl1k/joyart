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

        try {
            // Визначаємо диск для зберігання (azure в хмарі або public локально)
            $disk = env('APP_ENV') === 'production' ? 'azure' : 'public';

            // Delete the old avatar if it exists
            if ($user->avatar) {
                // Отримуємо тільки шлях файлу без URL
                $oldPath = str_replace(Storage::disk($disk)->url(''), '', $user->avatar);
                if (Storage::disk($disk)->exists($oldPath)) {
                    Storage::disk($disk)->delete($oldPath);
                }
            }

            // Store the file to the appropriate disk
            $path = $this->avatar->store('avatars', $disk);

            // Get the full URL to the file
            $fileUrl = Storage::disk($disk)->url($path);

            // Save the new path to the database
            $user->avatar = $fileUrl;
            $user->save();

            session()->flash('message', 'Аватар оновлено!');
            $this->reset('avatar');
            $this->previewImage = null;
        } catch (\Exception $e) {
            session()->flash('error', 'Помилка при завантаженні аватара: ' . $e->getMessage());
        }
    }

    public function render()
    {
        return view('livewire.profile.update-avatar-form');
    }
}
