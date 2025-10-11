<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;
use Kreait\Firebase\Contract\Auth as FirebaseAuth;

class ScheduleController extends Controller
{
    protected $database;
    protected $table = 'schedules';
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

    // VIEW
    public function view()
    {
        $uid = $this->getUid();

        $schedules = $this->database->getReference($this->table)->getValue();
        $result = [];

        if ($schedules) {
            foreach ($schedules as $id => $schedule) {
                if (($schedule['user_id'] ?? null) === $uid) {
                    $result[$id] = $schedule;
                }
            }
        }

        return view('schedules.index', compact('result'));
    }

    // READ ALL (API)
    public function index()
    {
        $uid = $this->getUid();
        $schedules = $this->database->getReference($this->table)->getValue();

        $result = [];
        if ($schedules) {
            foreach ($schedules as $id => $schedule) {
                if (($schedule['user_id'] ?? null) === $uid) {
                    $result[] = [
                        'id'          => $id,
                        'course_name' => $schedule['course_name'] ?? '',
                        'day'         => $schedule['day'] ?? '',
                        'start_time'  => $schedule['start_time'] ?? '',
                        'end_time'    => $schedule['end_time'] ?? '',
                        'room'        => $schedule['room'] ?? '',
                    ];
                }
            }
        }

        return response()->json(['data' => $result]);
    }

    // CREATE
    public function store(Request $request)
    {
        $uid = $this->getUid();

        $newSchedule = $this->database
            ->getReference($this->table)
            ->push([
                'course_name' => $request->course_name,
                'day'         => $request->day,
                'start_time'  => $request->start_time,
                'end_time'    => $request->end_time,
                'room'        => $request->room,
                'user_id'     => $uid,
            ]);

        return response()->json(['status' => 'success', 'id' => $newSchedule->getKey()]);
    }

    // UPDATE
    public function update(Request $request, $id)
    {
        $uid = $this->getUid();
        $schedule = $this->database->getReference($this->table . '/' . $id)->getValue();

        if (!$schedule || ($schedule['user_id'] ?? null) !== $uid) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $this->database->getReference($this->table . '/' . $id)->update([
            'course_name' => $request->course_name,
            'day'         => $request->day,
            'start_time'  => $request->start_time,
            'end_time'    => $request->end_time,
            'room'        => $request->room,
        ]);

        return response()->json(['status' => 'updated']);
    }

    // DELETE
    public function destroy($id)
    {
        $uid = $this->getUid();
        $schedule = $this->database->getReference($this->table . '/' . $id)->getValue();

        if (!$schedule || ($schedule['user_id'] ?? null) !== $uid) {
            return response()->json(['status' => 'error', 'message' => 'Unauthorized'], 403);
        }

        $this->database->getReference($this->table . '/' . $id)->remove();
        return response()->json(['status' => 'deleted']);
    }
}
