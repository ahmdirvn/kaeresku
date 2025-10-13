<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

class DashboardController extends Controller
{
    protected $database;
    protected $firebaseAuth;
    protected $uid;

    public function __construct(FirebaseAuth $firebaseAuth)
    {
        $this->database = Firebase::database();
        $this->firebaseAuth = $firebaseAuth;

        try {
            $token = session('firebase_token');
            if ($token) {
                try {
                    $verifiedIdToken = $firebaseAuth->verifyIdToken($token);
                    $this->uid = $verifiedIdToken->claims()->get('sub');
                } catch (\Throwable $e) {
                    $this->uid = null;
                    session()->forget('firebase_token'); // hapus token rusak
                }
            } else {
                $this->uid = null;
            }
        } catch (\Throwable $e) {
            $this->uid = null;
        }

        if (!$this->uid) {
            redirect()->route('login')->send();
        }
    }

    protected function getUid()
    {
        return $this->uid;
    }

    public function index()
    {
        if (!$this->uid) {
            return redirect()->route('login');
        }
        return view('dashboard.index');
    }

    public function getStats()
    {
        try {
            $uid = $this->getUid();

            // Ambil semua data
            $courses   = $this->database->getReference('courses')->getSnapshot()->getValue() ?? [];
            $lecturers = $this->database->getReference('lecturers')->getSnapshot()->getValue() ?? [];
            $schedules = $this->database->getReference('schedules')->getSnapshot()->getValue() ?? [];
            $krs       = $this->database->getReference('krs')->getSnapshot()->getValue() ?? [];

            // Filter manual tanpa fn()
            $userCourses = array_filter($courses, function ($item) use ($uid) {
                return isset($item['user_id']) && $item['user_id'] === $uid;
            });

            $userLecturers = array_filter($lecturers, function ($item) use ($uid) {
                return isset($item['user_id']) && $item['user_id'] === $uid;
            });

            $userSchedules = array_filter($schedules, function ($item) use ($uid) {
                return isset($item['user_id']) && $item['user_id'] === $uid;
            });

            $userKrs = isset($krs[$uid]) ? $krs[$uid] : [];

            $data = [
                'total_courses'   => count($userCourses),
                'total_lecturers' => count($userLecturers),
                'total_schedules' => count($userSchedules),
                'total_krs'       => count($userKrs),
            ];

            return response()->json([
                'status' => 'success',
                'data'   => $data
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status'  => 'error',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}
