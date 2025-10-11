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



        if (!$this->uid) {
            redirect()->route('login')->send();
        }
    }

    protected function getUid()
    {
        return $this->uid;
    }

    // VIEW (tetap untuk tampilan)
    public function view()
    {
        $uid = $this->getUid();

        $courses = $this->database->getReference($this->table)->getValue();

        // Ambil semua data lecturer
        $lecturers = $this->database->getReference('lecturers')->getValue();

        $result = [];


        if ($courses) {
            foreach ($courses as $id => $course) {
                // Filter berdasarkan user login
                if (($course['user_id'] ?? null) === $uid) {

                    // Ambil lecturer_id dari course
                    $lecturerId = $course['lecturer_id'] ?? null;

                    // Cek apakah lecturer_id ada di data lecturer
                    if ($lecturerId && isset($lecturers[$lecturerId])) {
                        $course['id'] = $id;
                        $course['lecturer_name'] = $lecturers[$lecturerId]['lecturer_name'] ?? 'Belum Diisi';
                        $course['lecturer_id'] = $lecturerId;
                    } else {
                        $course['id'] = $id;
                        $course['lecturer_name'] = 'Belum Diisi';
                    }

                    // Masukkan ke result
                    $result[$id] = $course;
                }
            }
        }


        // return response()->json(['data' => array_values($result)]);

        return view('courses.index', compact('result'));
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
                'user_id'     => $uid,
                'lecturer_id'  => $request->lecturerId ?? '',
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

        // Ambil semua data lecturer
        $lecturers = $this->database->getReference('lecturers')->getValue();

        $result = [];


        if ($courses) {
            foreach ($courses as $id => $course) {
                // Filter berdasarkan user login
                if (($course['user_id'] ?? null) === $uid) {

                    // Ambil lecturer_id dari course
                    $lecturerId = $course['lecturer_id'] ?? null;

                    // Cek apakah lecturer_id ada di data lecturer
                    if ($lecturerId && isset($lecturers[$lecturerId])) {
                        $course['id'] = $id;
                        $course['lecturer_name'] = $lecturers[$lecturerId]['lecturer_name'] ?? 'Belum Diisi';
                        $course['lecturer_id'] = $lecturerId;
                    } else {
                        $course['id'] = $id;
                        $course['lecturer_name'] = 'Belum Diisi';
                    }

                    // Masukkan ke result
                    $result[$id] = $course;
                }
            }
        }


        return response()->json(['data' => array_values($result)]);
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
                'lecturer_id'  => $request->lecturerId ?? '',

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
