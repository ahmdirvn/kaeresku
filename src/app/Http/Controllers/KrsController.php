<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

class KrsController extends Controller
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

    // === VIEW ===
    public function view()
    {
        $uid = $this->getUid();
        $data = $this->database->getReference($this->table)->getValue();
        $result = [];

        if ($data) {
            foreach ($data as $id => $krs) {
                if (($krs['user_id'] ?? null) === $uid) {
                    $result[] = [
                        'id' => $id,
                        'name' => $krs['name'] ?? '',
                        'sks' => $krs['sks'] ?? 0,
                        'description' => $krs['description'] ?? ''
                    ];
                }
            }
        }

        // return response()->json(['data' => $result]);
        return view('krs.view', compact('result'));
    }

    // === GET all KRS (per user) ===
    public function index()
    {
        $uid = $this->getUid();
        $data = $this->database->getReference($this->table)->getValue();
        $result = [];

        if ($data) {
            foreach ($data as $id => $krs) {
                if (($krs['user_id'] ?? null) === $uid) {
                    $result[] = [
                        'id' => $id,
                        'semester' => $krs['semester'] ?? '',
                        'max_sks' => $krs['max_sks'] ?? 0,
                        'description' => $krs['description'] ?? '',
                        'courses' => $krs['courses'] ?? []
                    ];
                }
            }
        }

        return response()->json(['data' => $result]);
    }

    // === CREATE semester ===
    public function store(Request $request)
    {
        $uid = $this->getUid();

        $new = $this->database->getReference($this->table)->push([
            'semester' => $request->semester ?? $request->name,
            'max_sks' => (int) $request->max_sks,
            'description' => $request->description ?? '',
            'courses' => [],
            'user_id' => $uid,
        ]);

        return response()->json([
            'status' => 'success',
            'id' => $new->getKey(),
            'data' => $new->getValue()
        ]);
    }

    // === UPDATE semester (nama / courses / sks) ===
    public function update(Request $request, $id)
    {
        $uid = $this->getUid();
        $krs = $this->database->getReference($this->table . '/' . $id)->getValue();

        if (!$krs || ($krs['user_id'] ?? null) !== $uid) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $data = [];
        if ($request->has('semester')) $data['semester'] = $request->semester;
        if ($request->has('max_sks')) $data['max_sks'] = (int) $request->max_sks;
        if ($request->has('description')) $data['description'] = $request->description;
        if ($request->has('courses')) $data['courses'] = $request->courses;

        $this->database->getReference($this->table . '/' . $id)->update($data);

        return response()->json(['status' => 'updated']);
    }

    // === DELETE semester ===
    public function destroy($id)
    {
        $uid = $this->getUid();
        $krs = $this->database->getReference($this->table . '/' . $id)->getValue();

        if (!$krs || ($krs['user_id'] ?? null) !== $uid) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $this->database->getReference($this->table . '/' . $id)->remove();
        return response()->json(['status' => 'deleted']);
    }
}
