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
        if (session()->has('firebase_token')) {
            return redirect()->route('dashboard');
        }
        return view('auth.login');
    }

    // Handle login
    public function login(Request $request)
    {
        // kalau sudah ada session token → redirect ke dashboard

        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required']
        ]);

        $email = $credentials['email'];
        $password = $credentials['password'];

        try {
            $signInResult = $this->firebaseAuth->signInWithEmailAndPassword($email, $password);
            $message = 'Successfully signed in!';
            // $token  = $signInResult->data()['idToken'];

            $idToken = $signInResult->idToken();              // token untuk validasi
            $refreshToken = $signInResult->refreshToken();    // simpan untuk refresh nanti

            // simpan di session
            session([
                'firebase_token' => $idToken,
                'firebase_refresh_token' => $refreshToken,
            ]);

            return redirect()->route('dashboard')->with('success', $message);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }

        return back()->withErrors([
            'error' => 'Email atau password salah!',
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


            $createdUser = $this->firebaseAuth->createUserWithEmailAndPassword($email, $password);

            $request->session()->put('firebase_user', [
                'uid' => $createdUser->uid,
                'email' => $createdUser->email,
                'name' => $validated['name'],
            ]);

            return redirect()->route('login')->with('success', 'Registrasi berhasil! . Silahkan Login menggunakan identitas terdaftar!');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }

    // Logout
    public function logout(Request $request)
    {
        // hapus token firebase
        $request->session()->forget('firebase_token');

        // invalidate & regenerate biar session fresh
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Berhasil logout!');
    }
}
