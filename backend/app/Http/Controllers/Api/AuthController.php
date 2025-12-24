<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    /**
     * 🔹 Реєстрація користувача
     */

    public function redirectToGoogle()
    {
        return Socialite::driver('google')->stateless()->redirect();
    }

    public function handleGoogleCallback()
    {
        try {
            $googleUser = Socialite::driver('google')->stateless()->user();


            $user = User::where('email', $googleUser->getEmail())->first();

            if ($user) {

                $user->update([
                    'google_id' => $googleUser->getId(),
                ]);
            } else {

                $user = User::create([
                    'first_name' => $googleUser->offsetGet('given_name') ?? $googleUser->getName(),
                    'last_name'  => $googleUser->offsetGet('family_name') ?? '',
                    'email'      => $googleUser->getEmail(),
                    'google_id'  => $googleUser->getId(),
                    'phone'      => '',
                    'password'   => null,
                ]);
            }


            $token = $user->createToken('auth_token')->plainTextToken;


            return redirect("http://localhost:3000/auth-callback?token={$token}");

        } catch (\Exception $e) {

            return redirect("http://localhost:3000/login?error=social_auth_failed");
        }
    }

    public function register(Request $request)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name'  => 'required|string|max:255',
            'phone'      => 'required|string|max:15|unique:users,phone',
            'email'      => 'required|string|email|max:255|unique:users,email',
            'password'   => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'first_name' => $validated['first_name'],
            'last_name'  => $validated['last_name'],
            'phone'      => $validated['phone'],
            'email'      => $validated['email'],
            'password'   => $validated['password'],
        ]);

        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Реєстрація успішна!',
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * 🔹 Авторизація користувача
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (!Auth::attempt($credentials)) {
            throw ValidationException::withMessages([
                'email' => ['Невірна електронна пошта або пароль.'],
            ]);
        }

        $user = Auth::user();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'status' => 'success',
            'message' => 'Вхід успішний!',
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * 🔹 Вихід (видалення токена)
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Ви вийшли із системи.',
        ]);
    }

    /**
     * 🔹 Перевірка авторизованого користувача
     */
    public function me(Request $request)
    {
        return response()->json([
            'user' => $request->user(),
        ]);
    }
}