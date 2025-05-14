<?php

namespace k1fl1k\joyart\App\Livewire\Profile;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Str;

class UpdateAvatarForm extends Component
{
    use WithFileUploads;

    public $avatar;
    public $previewImage = null;
    public $uploadError = null;

    public function mount()
    {
        Log::info('UpdateAvatarForm component mounted');
    }

    public function updatedAvatar()
    {
        Log::info('updatedAvatar method called');
        $this->uploadError = null;

        if (!$this->avatar) {
            Log::warning('Avatar is null in updatedAvatar method');
            $this->uploadError = 'Файл не вибрано';
            return;
        }

        try {
            Log::info('Avatar file info in updatedAvatar: ' . json_encode([
                'name' => $this->avatar->getClientOriginalName(),
                'size' => $this->avatar->getSize(),
                'mime' => $this->avatar->getMimeType(),
                'extension' => $this->avatar->getClientOriginalExtension(),
            ]));

            // Validate file type and size directly
            $extension = strtolower($this->avatar->getClientOriginalExtension());
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                $this->uploadError = 'Непідтримуваний тип файлу. Дозволені типи: jpg, jpeg, png, gif';
                $this->reset('avatar');
                return;
            }

            if ($this->avatar->getSize() > 2 * 1024 * 1024) { // 2MB
                $this->uploadError = 'Файл занадто великий. Максимальний розмір: 2MB';
                $this->reset('avatar');
                return;
            }

            // Try to generate preview
            try {
                Log::info('Trying to generate preview image in updatedAvatar');
                $this->previewImage = $this->avatar->temporaryUrl();
                Log::info('Preview image generated successfully in updatedAvatar');
            } catch (\Exception $e) {
                // If we can't generate a preview, continue anyway
                $this->previewImage = null;
                Log::warning('Could not generate preview, but continuing: ' . $e->getMessage());
            }
        } catch (\Exception $e) {
            $this->uploadError = 'Помилка при обробці файлу: ' . $e->getMessage();
            Log::error('Error in updatedAvatar: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            $this->reset('avatar');
        }
    }

    public function save()
    {
        Log::info('Save method called');

        try {
            // Check if we have a file
            if (!$this->avatar) {
                $this->uploadError = 'Файл не вибрано. Будь ласка, виберіть зображення.';
                Log::error('Avatar is null in save method');
                return;
            }

            // Get authenticated user
            $user = Auth::user();
            if (!$user) {
                $this->uploadError = 'Користувач не авторизований';
                Log::error('User not authenticated');
                return;
            }

            // Get file info
            $extension = strtolower($this->avatar->getClientOriginalExtension());
            $fileSize = $this->avatar->getSize();
            $mimeType = $this->avatar->getMimeType();

            Log::info('Processing avatar file: ' . json_encode([
                'name' => $this->avatar->getClientOriginalName(),
                'size' => $fileSize,
                'mime' => $mimeType,
                'extension' => $extension,
            ]));

            // Validate file manually
            if (!in_array($extension, ['jpg', 'jpeg', 'png', 'gif'])) {
                $this->uploadError = 'Непідтримуваний тип файлу. Дозволені типи: jpg, jpeg, png, gif';
                return;
            }

            if ($fileSize > 2 * 1024 * 1024) { // 2MB
                $this->uploadError = 'Файл занадто великий. Максимальний розмір: 2MB';
                return;
            }

            // Create a unique filename
            $filename = 'avatar_' . $user->id . '_' . time() . '_' . Str::random(8) . '.' . $extension;
            $storagePath = 'avatars';
            $fullPath = storage_path('app/public/' . $storagePath . '/' . $filename);
            $publicUrl = '/storage/' . $storagePath . '/' . $filename;

            Log::info('Generated filename: ' . $filename);
            Log::info('Full path: ' . $fullPath);
            Log::info('Public URL: ' . $publicUrl);

            // Ensure the directory exists
            $directory = storage_path('app/public/' . $storagePath);
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
                Log::info('Created directory: ' . $directory);
            }

            // Delete old avatar if it exists
            if ($user->avatar) {
                try {
                    $oldPath = public_path(parse_url($user->avatar, PHP_URL_PATH));
                    Log::info('Old avatar path: ' . $oldPath);

                    if (file_exists($oldPath)) {
                        unlink($oldPath);
                        Log::info('Deleted old avatar: ' . $oldPath);
                    } else {
                        Log::warning('Old avatar file not found: ' . $oldPath);
                    }
                } catch (\Exception $e) {
                    Log::error('Error deleting old avatar: ' . $e->getMessage());
                    // Continue execution
                }
            }

            // Save the file directly
            try {
                // Get the file contents
                $fileContents = file_get_contents($this->avatar->getRealPath());

                // Save the file to disk
                file_put_contents($fullPath, $fileContents);
                Log::info('Saved avatar file to: ' . $fullPath);

                // Update user record
                $user->avatar = $publicUrl;
                $user->save();
                Log::info('Updated user avatar in database');

                session()->flash('message', 'Аватар оновлено!');
                $this->reset(['avatar', 'previewImage', 'uploadError']);
            } catch (\Exception $e) {
                $this->uploadError = 'Помилка при збереженні файлу: ' . $e->getMessage();
                Log::error('Error saving file: ' . $e->getMessage());
                Log::error('Error trace: ' . $e->getTraceAsString());
            }
        } catch (\Exception $e) {
            $this->uploadError = 'Помилка при завантаженні аватара: ' . $e->getMessage();
            Log::error('Avatar upload error: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
        }
    }

    // Removed handleFileUpload method as it's redundant with updatedAvatar

    public function render()
    {
        return view('livewire.profile.update-avatar-form');
    }
}
