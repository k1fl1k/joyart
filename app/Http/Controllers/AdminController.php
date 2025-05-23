<?php

namespace k1fl1k\joyart\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;
use k1fl1k\joyart\Models\Artwork;
use k1fl1k\joyart\Models\Report;
use k1fl1k\joyart\Models\Tag;
use k1fl1k\joyart\Models\User;

class AdminController extends Controller
{
    public function index()
    {
        $usersCount = User::count();
        $artworksCount = Artwork::count();
        $tagsCount = Tag::count();
        $pendingReportsCount = Report::where('status', Report::STATUS_PENDING)->count();

        return view('admin.panel', compact('usersCount', 'artworksCount', 'tagsCount', 'pendingReportsCount'));
    }

    public function updateUser(Request $request)
    {
        $user = User::where('username', $request->username)->first();

        if ($user) {
            $user->update([
                'description' => $request->description,
                'email' => $request->email,
                'birthday' => $request->birthday,
                'allow_adult' => $request->has('allow_adult'),
                'role' => $request->role,
            ]);

            return redirect()->route('admin.panel')->with('status', 'Інформацію користувача оновлено!');
        }

        return redirect()->route('admin.panel')->with('error', 'Користувача не знайдено!');
    }

    public function searchUser(Request $request)
    {
        $query = $request->get('query');
        $users = User::where('username', 'ILIKE', "%$query%")
            ->select('id', 'username', 'birthday', 'description', 'role')
            ->limit(10)
            ->get();

        return response()->json($users);
    }

    public function getUserInfo($id)
    {
        $user = User::findOrFail($id);
        return response()->json($user);
    }

    public function fetchSafebooru(Request $request)
    {
        $user = Auth::user(); // Отримуємо поточного користувача
        $count = $request->count;

        // Виконати команду Artisan з передачею користувача
        Artisan::call('fetch:safebooru', [
            'count' => $count,
            'userId' => $user->id,
        ]);

        return redirect()->route('admin.panel')->with('status', "{$count} артворків було завантажено!");
    }
}

