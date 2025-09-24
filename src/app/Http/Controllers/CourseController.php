<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

class CourseController extends Controller
{
    protected $database;
    protected $table = 'courses';
    public $uid;

    protected $firebaseAuth;

    public function __construct(FirebaseAuth $firebaseAuth)
    {
        $this->database = Firebase::database();
        $this->firebaseAuth = $firebaseAuth;

        try {
            $token = session('firebase_token');
            if (!$token) {
                throw new \Exception('Token tidak ditemukan');
            }
            $verifiedIdToken = $firebaseAuth->verifyIdToken($token);
            $this->uid = $verifiedIdToken->claims()->get('sub'); // UID Firebase
        } catch (\Throwable $e) {
            $this->uid = null;
        }
    }

    protected function getUid()
    {
        return $this->uid;
    }

    // VIEW (tetap untuk tampilan)
    public function view()
    {
        $courses = $this->database->getReference($this->table)->getValue();
        return view('courses.index', compact('courses'));
    }

    // CREATE
    public function store(Request $request)
    {
        $uid = $this->getUid();

        $newCourse = $this->database
            ->getReference($this->table)
            ->push([
                'name'        => $request->name,
                'code'        => $request->code,
                'sks'         => $request->sks,
                'description' => $request->description ?? '',
                'category'    => $request->category ?? '',
                'user_id'     => $uid, // simpan id user
            ]);

        return response()->json([
            'status' => 'success',
            'id'     => $newCourse->getKey(),
            'data'   => $newCourse->getValue()
        ]);
    }

    // READ ALL (khusus data milik user login)
    public function index()
    {
        $uid = $this->getUid();
        $courses = $this->database->getReference($this->table)->getValue();

        $result = [];
        if ($courses) {
            foreach ($courses as $id => $course) {
                if (($course['user_id'] ?? null) === $uid) { // filter berdasarkan user_id
                    $result[] = [
                        'id'          => $id,
                        'name'        => $course['name'] ?? '',
                        'code'        => $course['code'] ?? '',
                        'sks'         => $course['sks'] ?? 0,
                        'description' => $course['description'] ?? '',
                        'category'    => $course['category'] ?? '',
                    ];
                }
            }
        }

        return response()->json(['data' => $result]);
    }

    // UPDATE (hanya boleh update data milik user)
    public function update(Request $request, $id)
    {
        $uid = $this->getUid();
        $course = $this->database->getReference($this->table . '/' . $id)->getValue();

        if (!$course || ($course['user_id'] ?? null) !== $uid) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $this->database->getReference($this->table . '/' . $id)
            ->update([
                'name'        => $request->name,
                'code'        => $request->code,
                'sks'         => $request->sks,
                'description' => $request->description ?? '',
                'category'    => $request->category ?? '',
            ]);

        return response()->json(['status' => 'updated']);
    }

    // DELETE (hanya boleh hapus data milik user)
    public function destroy($id)
    {
        $uid = $this->getUid();
        $course = $this->database->getReference($this->table . '/' . $id)->getValue();

        if (!$course || ($course['user_id'] ?? null) !== $uid) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $this->database->getReference($this->table . '/' . $id)->remove();
        return response()->json(['status' => 'deleted']);
    }
}
