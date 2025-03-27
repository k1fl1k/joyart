<?php

namespace k1fl1k\joyart\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{
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
}
