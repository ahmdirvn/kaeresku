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
        $this->database = \Kreait\Laravel\Firebase\Facades\Firebase::database();
        $this->firebaseAuth = $firebaseAuth;

        try {
            $token = session('firebase_token'); //ini di dd ada
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


    public function view()
    {
        $courses = $this->database->getReference($this->table)->getValue();
        return view('courses.index', compact('courses'));
    }

    //  CREATE
    public function store(Request $request)
    {
        $token = session('firebase_token');
        $verifiedIdToken = $this->firebaseAuth->verifyIdToken($token);
        $uid = $verifiedIdToken->claims()->get('sub');

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

    //  READ ALL
    public function index()
    {
        $courses = $this->database->getReference($this->table)->getValue();

        $result = [];
        if ($courses) {
            foreach ($courses as $id => $course) {
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

        return response()->json(['data' => $result]);
    }

    //  UPDATE
    public function update(Request $request, $id)
    {
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

    //  DELETE
    public function destroy($id)
    {
        $this->database->getReference($this->table . '/' . $id)->remove();
        return response()->json(['status' => 'deleted']);
    }
}
