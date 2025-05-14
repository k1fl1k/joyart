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

    public function mount()
    {
        Log::info('UpdateAvatarForm component mounted');
    }

    public function updatedAvatar()
    {
        Log::info('updatedAvatar method called');

        if (!$this->avatar) {
            Log::warning('Avatar is null in updatedAvatar method');
            return;
        }

        Log::info('Avatar file info in updatedAvatar: ' . json_encode([
            'name' => $this->avatar->getClientOriginalName(),
            'size' => $this->avatar->getSize(),
            'mime' => $this->avatar->getMimeType(),
            'extension' => $this->avatar->getClientOriginalExtension(),
        ]));

        $this->validate([
            'avatar' => 'image|max:2048',
        ]);

        try {
            // Генерація попереднього перегляду зображення
            Log::info('Trying to generate preview image in updatedAvatar');
            $this->previewImage = $this->avatar->temporaryUrl();
            Log::info('Preview image generated successfully in updatedAvatar');

            // Викликаємо handleFileUpload для додаткової обробки
            $this->handleFileUpload();
        } catch (\Exception $e) {
            // Якщо не вдалося отримати тимчасовий URL, використовуємо заглушку
            $this->previewImage = null;
            Log::error('Error generating preview in updatedAvatar: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
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
            // Використовуємо завжди локальний диск public
            $disk = 'public';
            Log::info('Using disk: ' . $disk);

            // Створюємо унікальне ім'я файлу
            $filename = 'avatar_' . $user->id . '_' . time() . '.' . $this->avatar->getClientOriginalExtension();
            Log::info('Generated filename: ' . $filename);

            // Видаляємо старий аватар, якщо він існує
            if ($user->avatar) {
                try {
                    // Отримуємо шлях файлу з URL
                    $oldPath = public_path(str_replace(env('APP_URL'), '', $user->avatar));
                    Log::info('Old avatar path: ' . $oldPath);

                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                        Log::info('Deleted old avatar: ' . $oldPath);
                    } else {
                        Log::warning('Old avatar file not found: ' . $oldPath);
                    }
                } catch (\Exception $e) {
                    Log::error('Error deleting old avatar: ' . $e->getMessage());
                    // Продовжуємо виконання
                }
            }

            // Зберігаємо файл в публічну директорію
            try {
                $path = $this->avatar->storeAs('avatars', $filename, $disk);
                Log::info('Stored new avatar at: ' . $path);
            } catch (\Exception $e) {
                Log::error('Error storing avatar: ' . $e->getMessage());
                Log::error('Error trace: ' . $e->getTraceAsString());
                throw $e;
            }

            // Формуємо URL до файлу
            $fileUrl = env('APP_URL') . '/storage/avatars/' . $filename;
            Log::info('New avatar URL: ' . $fileUrl);

            // Зберігаємо шлях в базу даних
            $user->avatar = $fileUrl;
            $user->save();
            Log::info('Updated user avatar in database');

            session()->flash('message', 'Аватар оновлено!');
            $this->reset('avatar');
            $this->previewImage = null;
        } catch (\Exception $e) {
            Log::error('Avatar upload error: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            session()->flash('error', 'Помилка при завантаженні аватара: ' . $e->getMessage());
        }
    }

    public function handleFileUpload()
    {
        Log::info('handleFileUpload method called');

        if (!$this->avatar) {
           Log::warning('Avatar is null in handleFileUpload method');
            return;
        }

        Log::info('Avatar file info: ' . json_encode([
            'name' => $this->avatar->getClientOriginalName(),
            'size' => $this->avatar->getSize(),
            'mime' => $this->avatar->getMimeType(),
            'extension' => $this->avatar->getClientOriginalExtension(),
        ]));

        $this->validate([
            'avatar' => 'image|max:2048',
        ]);

        try {
            // Генерація попереднього перегляду зображення
            Log::info('Trying to generate preview image');
            $this->previewImage = $this->avatar->temporaryUrl();
            Log::info('Preview image generated successfully');
        } catch (\Exception $e) {
            // Якщо не вдалося отримати тимчасовий URL, використовуємо заглушку
            $this->previewImage = null;
            Log::error('Error generating preview: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            session()->flash('warning', 'Не вдалося створити попередній перегляд, але ви все одно можете завантажити файл.');
        }
    }

    public function render()
    {
        return view('livewire.profile.update-avatar-form');
    }
}
