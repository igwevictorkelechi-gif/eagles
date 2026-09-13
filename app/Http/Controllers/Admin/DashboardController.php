<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\SchoolClass;
use App\Models\Subject;
use App\Models\Exam;
use App\Models\Transaction;
use App\Models\StudentFee;
use App\Models\Announcement;

class DashboardController extends Controller
{
    public function index()
    {
        $counts = [
            'students' => Student::count(),
            'teachers' => Teacher::count(),
            'classes' => SchoolClass::count(),
            'subjects' => Subject::count(),
            'exams' => Exam::count(),
        ];
        $income = (int) Transaction::where('type', 'income')->sum('amount');
        $expense = (int) Transaction::where('type', 'expense')->sum('amount');
        $outstanding = (int) StudentFee::sum('amount') - (int) StudentFee::sum('amount_paid');

        return view('app.dashboard', [
            'counts' => $counts,
            'finance' => [
                'income' => $income,
                'expense' => $expense,
                'profit' => $income - $expense,
                'outstanding' => max(0, $outstanding),
            ],
            'announcements' => Announcement::orderByDesc('created_at')->limit(5)->get(),
        ]);
    }
}
