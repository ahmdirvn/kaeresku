<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\FirebaseService;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

class AuthenticationController extends Controller
{
    protected $firebaseAuth;

    public function __construct(FirebaseAuth $firebaseAuth)

    {

        $this->firebaseAuth = $firebaseAuth;
    }

    // Show login form
    public function showLogin()
    {
        return view('auth.login');
    }

    // Handle login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('/dashboard'); // ganti sesuai route dashboard
        }

        return back()->withErrors([
            'email' => 'Email atau password salah!',
        ]);
    }

    // Show register form
    public function showRegister()
    {
        return view('auth.register');
    }

    // Handle register
    // Handle register
    public function register(Request $request)
    {

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email'],
            'password' => ['required', 'confirmed', 'min:6'],
            'terms' => ['accepted'], // biar checkbox terms wajib
        ]);

        try {
            $userProperties = [
                'email' => $validated['email'],
                'emailVerified' => false,
                'password' => $validated['password'],
                'displayName' => $validated['name'],
                'disabled' => false,
            ];

            $email = $validated['email'];
            $password = $validated['password'];

            // try {
            //     $createdUser = $this->firebaseAuth->createUserWithEmailAndPassword($email, $password);
            // } catch (\Throwable $e) {
            //     dd('Error Firebase:', $e->getMessage());
            // }

            $createdUser = $this->firebaseAuth->createUserWithEmailAndPassword($email, $password);

            // bisa langsung login kalau mau
            $request->session()->put('firebase_user', [
                'uid' => $createdUser->uid,
                'email' => $createdUser->email,
                'name' => $validated['name'],
            ]);

            return redirect()->route('dashboard')->with('success', 'Registrasi berhasil!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/'); // kembali ke landing page
    }
}
