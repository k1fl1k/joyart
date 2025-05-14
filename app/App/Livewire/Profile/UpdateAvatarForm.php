<?php

namespace k1fl1k\joyart\App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
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

        try {
            // Генерація попереднього перегляду зображення
            $this->previewImage = $this->avatar->temporaryUrl();
        } catch (\Exception $e) {
            // Якщо не вдалося отримати тимчасовий URL, використовуємо заглушку
            $this->previewImage = null;
            session()->flash('warning', 'Не вдалося створити попередній перегляд, але ви все одно можете завантажити файл.');
        }
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
                try {
                    // Отримуємо тільки шлях файлу без URL
                    $oldPath = str_replace(Storage::disk($disk)->url(''), '', $user->avatar);
                    if (Storage::disk($disk)->exists($oldPath)) {
                        Storage::disk($disk)->delete($oldPath);
                    } else {
                        Log::warning('Old avatar file not found: ' . $oldPath);
                    }
                } catch (\Exception $e) {
                    Log::error('Error deleting old avatar: ' . $e->getMessage());
                    // Продовжуємо виконання, навіть якщо не вдалося видалити старий файл
                }
            }

            // Store the file to the appropriate disk
            try {
                $path = $this->avatar->store('avatars', $disk);
                Log::info('Stored new avatar at: ' . $path);
            } catch (\Exception $e) {
                Log::error('Error storing avatar: ' . $e->getMessage());
                Log::error('Error trace: ' . $e->getTraceAsString());
                throw $e; // Перекидаємо помилку далі для обробки в зовнішньому catch блоці
            }

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

    public function handleFileUpload()
    {
        if (!$this->avatar) {
           Log::warning('Avatar is null in handleFileUpload method');
            return;
        }

        $this->validate([
            'avatar' => 'image|max:2048',
        ]);

        try {
            // Генерація попереднього перегляду зображення
            $this->previewImage = $this->avatar->temporaryUrl();
        } catch (\Exception $e) {
            // Якщо не вдалося отримати тимчасовий URL, використовуємо заглушку
            $this->previewImage = null;
            session()->flash('warning', 'Не вдалося створити попередній перегляд, але ви все одно можете завантажити файл.');
        }
    }

    public function render()
    {
        return view('livewire.profile.update-avatar-form');
    }
}
