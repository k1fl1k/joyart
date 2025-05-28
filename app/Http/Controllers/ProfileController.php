<?php

namespace k1fl1k\joyart\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use k1fl1k\joyart\Models\Artwork;
use k1fl1k\joyart\Models\User;
use Illuminate\Validation\Rule;

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
                return response()->json(['error' => 'Користувач не авторизований'], 401);
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
                }
            }

            // Save the file
            $path = $file->storeAs('avatars', $filename, 'public');
            Log::info('Stored avatar at: ' . $path);

            // Update user record
            $avatarUrl = '/storage/' . $path;
            $user->avatar = $avatarUrl;
            $user->save();
            Log::info('Updated user avatar in database');

            return response()->json([
                'message' => 'Аватар оновлено!',
                'avatar_url' => $avatarUrl,
            ]);
        } catch (\Exception $e) {
            Log::error('Avatar upload error: ' . $e->getMessage());
            Log::error('Error trace: ' . $e->getTraceAsString());
            return response()->json(['error' => 'Помилка при завантаженні аватара: ' . $e->getMessage()], 500);
        }
    }

    public function updateProfile(Request $request)
    {
        Log::info('ProfileController@updateProfile called');

        try {
            $user = Auth::user();

            if (!$user) {
                Log::error('User not authenticated');
                return response()->json(['error' => 'Користувач не авторизований'], 401);
            }

            $validated = $request->validate([
                'username' => ['required', 'string', 'max:255', Rule::unique(User::class)->ignore($user->id)],
                'email' => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($user->id)],
            ]);

            $user->fill($validated);

            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }

            $user->save();
            Log::info('Updated user profile: ' . json_encode($validated));

            return response()->json([
                'message' => 'Профіль оновлено!',
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error: ' . json_encode($e->errors()));
            return response()->json(['errors' => $e->errors()], 422);
        } catch (\Exception $e) {
            Log::error('Profile update error: ' . $e->getMessage());
            return response()->json(['error' => 'Помилка при оновленні профілю: ' . $e->getMessage()], 500);
        }
    }

    public function sendVerification(Request $request)
    {
        Log::info('ProfileController@sendVerification called');

        try {
            $user = Auth::user();

            if (!$user) {
                Log::error('User not authenticated');
                return response()->json(['error' => 'Користувач не авторизований'], 401);
            }

            if ($user->hasVerifiedEmail()) {
                return response()->json(['message' => 'Електронна пошта вже підтверджена'], 200);
            }

            $user->sendEmailVerificationNotification();
            Log::info('Verification email sent to: ' . $user->email);

            return response()->json([
                'message' => 'Новий лист для підтвердження надіслано на вашу електронну адресу.',
            ]);
        } catch (\Exception $e) {
            Log::error('Verification email error: ' . $e->getMessage());
            return response()->json(['error' => 'Помилка при надсиланні листа для підтвердження: ' . $e->getMessage()], 500);
        }
    }
}
