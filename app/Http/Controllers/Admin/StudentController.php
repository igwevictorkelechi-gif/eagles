<?php

namespace App\Http\Controllers\Admin;

use App\Models\Student;
use App\Models\SchoolClass;

class StudentController extends ResourceController
{
    protected function config(): array
    {
        $classes = SchoolClass::orderBy('name')->pluck('name', 'id')->all();
        return [
            'model' => Student::class,
            'title' => 'Students',
            'singular' => 'Student',
            'subtitle' => 'Manage student records and enrollment.',
            'route' => 'app.students',
            'columns' => [
                ['key' => 'admission_no', 'label' => 'Adm. No'],
                ['label' => 'Name', 'value' => fn ($r) => trim($r->first_name . ' ' . $r->last_name)],
                ['key' => 'gender', 'label' => 'Gender'],
                ['label' => 'Class', 'value' => fn ($r) => $classes[$r->class_id] ?? '—'],
                ['key' => 'guardian_phone', 'label' => 'Guardian phone'],
                ['key' => 'status', 'label' => 'Status', 'badge' => true],
            ],
            'fields' => [
                ['name' => 'admission_no', 'label' => 'Admission No'],
                ['name' => 'first_name', 'label' => 'First name', 'required' => true],
                ['name' => 'last_name', 'label' => 'Last name'],
                ['name' => 'gender', 'label' => 'Gender', 'type' => 'select', 'options' => ['M' => 'Male', 'F' => 'Female']],
                ['name' => 'date_of_birth', 'label' => 'Date of birth', 'type' => 'date'],
                ['name' => 'class_id', 'label' => 'Class', 'type' => 'select', 'options' => $classes],
                ['name' => 'guardian_name', 'label' => 'Guardian name'],
                ['name' => 'guardian_phone', 'label' => 'Guardian phone'],
                ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive']],
            ],
        ];
    }
}
