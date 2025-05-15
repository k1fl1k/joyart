<?php

namespace k1fl1k\joyart\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use k1fl1k\joyart\Models\Artwork;
use k1fl1k\joyart\Models\User;

class ProfileController extends Controller
{
    public function show($username)
    {
        $user = User::where('username', $username)->firstOrFail();
        $userPosts = $user->artworks;
        $likedPosts = Artwork::whereHas('favorites', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->orderBy('created_at', 'desc')->get();

        return view('profile', compact('user', 'userPosts', 'likedPosts'));
    }

    public function startEditing(Request $request)
    {
        // Встановлюємо сесію для редагування опису
        $request->session()->put('editing_description', true);

        return redirect()->back();
    }

    public function updateDescription(Request $request)
    {
        // Валідація опису
        $request->validate([
            'description' => 'nullable|string',
        ]);

        // Оновлення опису користувача
        $user = Auth::user();
        $user->description = $request->description;
        $user->save();

        // Видалити сесію редагування після збереження
        $request->session()->forget('editing_description');

        return redirect()->back()->with('success', 'Description updated successfully.');
    }

    public function settings()
    {
        return view('settings');
    }

    public function updateAvatar(Request $request)
    {
        Log::info('ProfileController@updateAvatar called');

        $request->validate([
            'avatar' => 'required|image|max:2048',
        ]);

        try {
            $user = Auth::user();

            if (!$user) {
                Log::error('User not authenticated');
                return back()->with('error', 'Користувач не авторизований');
            }

            $file = $request->file('avatar');
            $extension = strtolower($file->getClientOriginalExtension());
            $filename = 'avatar_' . $user->id . '_' . time() . '_' . Str::random(8) . '.' . $extension;

            Log::info('Processing avatar file: ' . json_encode([
                'name' => $file->getClientOriginalName(),
                'size' => $file->getSize(),
                'mime' => $file->getMimeType(),
                'extension' => $extension,
            ]));

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

            // Save the file
            $path = $file->storeAs('avatars', $filename, 'public');
            Log::info('Stored avatar at: ' . $path);

            // Update user record
            $user->avatar = '/storage/' . $path;
            $user->save();
            Log::info('Updated user avatar in database');

            return back()->with('message', 'Аватар оновлено!');
        } catch (\Exception $e) {
            Log::error('Avatar upload error: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            return back()->with('error', 'Помилка при завантаженні аватара: ' . $e->getMessage());
        }
    }
}
