<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Kreait\Laravel\Firebase\Facades\Firebase;

class CourseController extends Controller
{
    //
    protected $database;
    protected $table = 'courses';

    public function __construct()
    {
        $this->database = Firebase::database();
    }


    public function view()
    {
        $courses = $this->database->getReference($this->table)->getValue();

        return view('courses.index', compact('courses'));
    }

    // CRUD SECTION

    public function store(Request $request)
    {
        $newCourse = $this->database
            ->getReference($this->table)
            ->push([
                'name' => $request->name,
                'code' => $request->code,
                'sks' => $request->sks,
            ]);

        return response()->json([
            'status' => 'success',
            'id' => $newCourse->getKey(),
            'data' => $newCourse->getValue()
        ]);
    }

    // ✅ READ ALL
    public function index()
    {
        $courses = $this->database->getReference($this->table)->getValue();

        $result = [];
        if ($courses) {
            foreach ($courses as $id => $course) {
                $result[] = [
                    'id'   => $id,
                    'name' => $course['name'] ?? '',
                    'code' => $course['code'] ?? '',
                    'sks'  => $course['sks'] ?? 0,
                ];
            }
        }

        return response()->json(['data' => $result]);
    }


    // ✅ UPDATE
    public function update(Request $request, $id)
    {
        $this->database->getReference($this->table . '/' . $id)
            ->update([
                'name' => $request->name,
                'code' => $request->code,
                'sks' => $request->sks,
            ]);

        return response()->json(['status' => 'updated']);
    }

    // ✅ DELETE
    public function destroy($id)
    {
        $this->database->getReference($this->table . '/' . $id)->remove();

        return response()->json(['status' => 'deleted']);
    }
}
