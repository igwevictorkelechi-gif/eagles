<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Result;
use App\Models\Student;
use App\Models\Subject;
use App\Support\Grades;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ResultController extends Controller
{
    public function index()
    {
        $students = Student::get()->mapWithKeys(fn ($s) => [$s->id => trim($s->first_name . ' ' . $s->last_name)]);
        $subjects = Subject::pluck('name', 'id');
        $rows = Result::orderByDesc('created_at')->limit(300)->get()->map(function ($r) use ($students, $subjects) {
            $r->student_name = $students[$r->student_id] ?? '—';
            $r->subject_name = $subjects[$r->subject_id] ?? '—';
            return $r;
        });
        return view('app.results', [
            'rows' => $rows,
            'students' => $students,
            'subjects' => $subjects,
            'isAdmin' => Auth::user()->role === 'school_admin',
        ]);
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'student_id' => ['required'],
            'subject_id' => ['nullable'],
            'ca_score' => ['nullable', 'numeric'],
            'exam_score' => ['nullable', 'numeric'],
        ]);
        $ca = (float) ($data['ca_score'] ?? 0);
        $exam = (float) ($data['exam_score'] ?? 0);
        $total = $ca + $exam;
        $g = Grades::for($total);
        Result::create([
            'student_id' => $data['student_id'],
            'subject_id' => $data['subject_id'] ?? null,
            'ca_score' => $ca,
            'exam_score' => $exam,
            'total_score' => $total,
            'grade' => $g['grade'],
            'remark' => $g['remark'],
            'status' => 'submitted',
            'entered_by' => Auth::id(),
        ]);
        return redirect()->route('app.results')->with('ok', 'Result recorded.');
    }

    public function setStatus(Request $request, string $id)
    {
        $data = $request->validate(['status' => ['required', 'in:draft,submitted,approved,rejected,locked']]);
        $r = Result::findOrFail($id);
        $r->update(['status' => $data['status']]);
        return redirect()->route('app.results')->with('ok', 'Result ' . $data['status'] . '.');
    }
}
