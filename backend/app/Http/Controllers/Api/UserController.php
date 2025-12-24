<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    public function update(Request $request)
    {
        $user = $request->user();

        $validated = $request->validate([
            'first_name' => 'nullable|string|max:255',
            'last_name'  => 'nullable|string|max:255',
            'phone'      => 'nullable|string|max:15|unique:users,phone,' . $user->id,
            'password'   => 'nullable|string|min:6', // дозволяємо null або порожній рядок
        ]);

        // Якщо пароль порожній — видаляємо його з масиву оновлення
        if (empty($request->password)) {
            unset($validated['password']);
        } else {
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        return response()->json([
            'status' => 'success',
            'user' => $user->fresh() // Повертаємо оновлені дані
        ]);
    }
}