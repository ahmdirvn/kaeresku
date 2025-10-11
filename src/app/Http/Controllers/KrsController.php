<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

class KrsController extends Controller
{
    protected $database;
    protected $tableCourses = 'courses';
    protected $tableKrs = 'krs';
    protected $firebaseAuth;
    protected $uid;

    public function __construct(FirebaseAuth $firebaseAuth)
    {
        $this->database = Firebase::database();
        $this->firebaseAuth = $firebaseAuth;

        try {
            $token = session('firebase_token');
            if (!$token) throw new \Exception('Token tidak ditemukan');
            $verified = $firebaseAuth->verifyIdToken($token);
            $this->uid = $verified->claims()->get('sub');
        } catch (\Throwable $e) {
            $this->uid = null;
        }

        if (!$this->uid) redirect()->route('login')->send();
    }

    protected function getUid()
    {
        return $this->uid;
    }

    // === VIEW PAGE ===
    public function view()
    {
        $uid = $this->getUid();
        $courses = $this->database->getReference($this->tableCourses)->getValue();
        $result = [];

        if ($courses) {
            foreach ($courses as $id => $course) {
                if (($course['user_id'] ?? null) === $uid) {
                    $result[] = [
                        'id' => $id,
                        'name' => $course['name'] ?? '',
                        'sks' => $course['sks'] ?? 0,
                        'description' => $course['description'] ?? '',
                    ];
                }
            }
        }

        return view('krs.view', compact('result'));
    }

    // === GET USER KRS ===
    public function index()
    {
        $uid = $this->getUid();
        $data = $this->database->getReference("{$this->tableKrs}/{$uid}")->getValue() ?? [];
        return response()->json(['data' => $data]);
    }

    // === SAVE USER KRS ===
    public function store(Request $request)
    {
        $uid = $this->getUid();
        $payload = $request->all();

        $this->database->getReference("{$this->tableKrs}/{$uid}")->set($payload);

        return response()->json(['status' => 'success', 'message' => 'KRS saved successfully']);
    }

    // === RESET USER KRS ===
    public function destroy()
    {
        $uid = $this->getUid();
        $this->database->getReference("{$this->tableKrs}/{$uid}")->remove();
        return response()->json(['status' => 'deleted']);
    }
}
