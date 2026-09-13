<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\Result;
use App\Models\SchoolClass;
use App\Models\Student;
use App\Models\StudentFee;
use App\Models\Subject;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        // Student rows are tenant-scoped; find the one linked to this user.
        $student = Student::where('user_id', $user->id)->first();

        $results = collect();
        $fees = collect();
        $className = null;
        if ($student) {
            $subjects = Subject::pluck('name', 'id');
            $results = Result::where('student_id', $student->id)
                ->whereIn('status', ['approved', 'locked'])
                ->get()
                ->map(function ($r) use ($subjects) {
                    $r->subject_name = $subjects[$r->subject_id] ?? '—';
                    return $r;
                });
            $fees = StudentFee::where('student_id', $student->id)->get();
            if ($student->class_id) {
                $className = optional(SchoolClass::find($student->class_id))->name;
            }
        }

        $announcements = Announcement::whereIn('audience', ['all', 'students'])
            ->orderByDesc('created_at')->limit(10)->get();

        return view('student.dashboard', [
            'user' => $user,
            'student' => $student,
            'className' => $className,
            'results' => $results,
            'fees' => $fees,
            'announcements' => $announcements,
        ]);
    }
}
