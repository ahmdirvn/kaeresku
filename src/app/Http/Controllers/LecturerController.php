<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

class LecturerController extends Controller
{
    protected $database;
    protected $table = 'lecturers';
    protected $firebaseAuth;
    public $uid;

    public function __construct(FirebaseAuth $firebaseAuth)
    {
        $this->database = Firebase::database();
        $this->firebaseAuth = $firebaseAuth;

        try {
            $token = session('firebase_token');
            if (!$token) throw new \Exception('Token tidak ditemukan');

            $verifiedIdToken = $firebaseAuth->verifyIdToken($token);
            $this->uid = $verifiedIdToken->claims()->get('sub');
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

    // === VIEW PAGE ===
    public function view()
    {
        return view('lecturers.index');
    }

    // === API: GET ALL ===
    public function index()
    {
        $uid = $this->getUid();
        $lecturers = $this->database->getReference($this->table)->getValue() ?? [];

        $result = [];
        foreach ($lecturers as $id => $lecturer) {
            if (($lecturer['user_id'] ?? null) === $uid) {
                $result[] = [
                    'id'            => $id,
                    'lecturer_id'   => $lecturer['lecturer_id'] ?? '',
                    'lecturer_name' => $lecturer['lecturer_name'] ?? '',
                    'lecturer_code' => $lecturer['lecturer_code'] ?? '',
                    'description'   => $lecturer['description'] ?? '',
                ];
            }
        }

        return response()->json(['data' => $result]);
    }

    // === STORE ===
    public function store(Request $request)
    {
        $uid = $this->getUid();

        $newLecturer = $this->database->getReference($this->table)->push([
            'lecturer_name' => $request->lecturer_name,
            'lecturer_code' => $request->lecturer_code,
            'description'   => $request->lecturer_description ?? '',
            'user_id'       => $uid,
        ]);

        $lecturerId = $newLecturer->getKey();
        $this->database->getReference($this->table . '/' . $lecturerId)
            ->update(['lecturer_id' => $lecturerId]);

        return response()->json(['status' => 'success']);
    }

    // === UPDATE ===
    public function update(Request $request, $id)
    {
        $uid = $this->getUid();
        $lecturer = $this->database->getReference($this->table . '/' . $id)->getValue();

        if (!$lecturer || ($lecturer['user_id'] ?? null) !== $uid) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $this->database->getReference($this->table . '/' . $id)->update([
            'lecturer_name' => $request->lecturer_name,
            'lecturer_code' => $request->lecturer_code,
            'description'   => $request->lecturer_description ?? '',
        ]);

        return response()->json(['status' => 'updated']);
    }

    // === DELETE ===
    public function destroy($id)
    {
        $uid = $this->getUid();
        $lecturer = $this->database->getReference($this->table . '/' . $id)->getValue();

        if (!$lecturer || ($lecturer['user_id'] ?? null) !== $uid) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $this->database->getReference($this->table . '/' . $id)->remove();
        return response()->json(['status' => 'deleted']);
    }
}
