<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Services\FirebaseService;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;
use Illuminate\Support\Facades\Mail;
use App\Mail\VerifyEmail;
use Kreait\Laravel\Firebase\Facades\Firebase;

class AuthenticationController extends Controller
{
    protected $firebaseAuth;
    protected $database;


    public function __construct(FirebaseAuth $firebaseAuth)

    {

        $this->firebaseAuth = $firebaseAuth;
        $this->database = Firebase::database();
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

            session()->save(); //save the session

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


            // $createdUser = $this->firebaseAuth->createUserWithEmailAndPassword($email, $password);

            $createdUser = $this->firebaseAuth->createUser([
                'email' => $email,
                'password' => $password,
            ]);

            // // getting link untuk smpt
            // $verificationLink = $this->firebaseAuth->getEmailVerificationLink($request->email);

            $actionCodeSettings = [
                'continueUrl' => "https://kaeresku-738039218648.asia-southeast2.run.app/verify?email=" . urlencode($email),
                'handleCodeInApp' => false,
            ];

            $this->firebaseAuth->sendEmailVerificationLink($email, $actionCodeSettings);

            // 3. Simpan ke Realtime Database (status awal belum verifikasi)
            // $userData = [
            //     'email' => $email,
            //     'is_verified' => false,
            // ];
            // $this->database
            //     ->getReference('users/' . md5($email))
            //     ->set($userData);

            // ini kalau mau pakai smtp
            // Generate verification link dari Firebase

            // Mail::to($email)->send(new VerifyEmail($verificationLink));

            $request->session()->put('firebase_user', [
                'uid' => $createdUser->uid,
                'email' => $createdUser->email,
                'name' => $validated['name'],
            ]);

            return redirect()->route('login')->with('success', 'Registrasi berhasil! Silakan cek email untuk verifikasi!');
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

    public function verifyEmail(Request $request)
    {
        $email = $request->query('email'); // ambil params ?email

        if (!$email) {
            return view('emails/verify', [
                'status' => 'error',
                'message' => 'Email tidak ditemukan.'
            ]);
        }

        try {
            // cek user di Firebase Auth
            $user = $this->firebaseAuth->getUserByEmail($email);

            if ($user->emailVerified) {
                // update ke Realtime DB
                $this->database
                    ->getReference('users/' . md5($email) . '/is_verified')
                    ->set(true);

                return view('emails.verify', [
                    'status' => 'success',
                    'message' => 'Email berhasil diverifikasi! Silakan login.'
                ]);
            } else {
                return view('emails.verify', [
                    'status' => 'warning',
                    'message' => 'Email belum diverifikasi. Silakan cek email Anda.'
                ]);
            }
        } catch (\Throwable $e) {
            return view('verify', [
                'status' => 'error',
                'message' => '❌ Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }
}
