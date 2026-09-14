<?php

namespace App\Http\Controllers\Admin;

use App\Models\Exam;
use App\Models\Subject;
use App\Models\SchoolClass;

class ExamController extends ResourceController
{
    protected function config(): array
    {
        $subjects = Subject::orderBy('name')->pluck('name', 'id')->all();
        $classes = SchoolClass::orderBy('name')->pluck('name', 'id')->all();
        return [
            'model' => Exam::class,
            'title' => 'Examinations',
            'singular' => 'Exam',
            'subtitle' => 'Create CBT and other examinations.',
            'route' => 'app.exams',
            'columns' => [
                ['key' => 'title', 'label' => 'Title'],
                ['key' => 'type', 'label' => 'Type'],
                ['label' => 'Duration', 'value' => fn ($r) => $r->duration_mins . ' min'],
                ['key' => 'pass_mark', 'label' => 'Pass mark'],
                ['key' => 'status', 'label' => 'Status', 'badge' => true],
            ],
            'fields' => [
                ['name' => 'title', 'label' => 'Exam title', 'required' => true],
                ['name' => 'type', 'label' => 'Type', 'type' => 'select', 'options' => [
                    'cbt' => 'CBT', 'class_test' => 'Class test', 'mock' => 'Mock',
                    'practice' => 'Practice', 'internal' => 'Internal', 'entrance' => 'Entrance',
                ]],
                ['name' => 'subject_id', 'label' => 'Subject', 'type' => 'select', 'options' => $subjects],
                ['name' => 'class_id', 'label' => 'Class', 'type' => 'select', 'options' => $classes],
                ['name' => 'duration_mins', 'label' => 'Duration (minutes)', 'type' => 'number'],
                ['name' => 'question_count', 'label' => 'Question count', 'type' => 'number'],
                ['name' => 'pass_mark', 'label' => 'Pass mark (%)', 'type' => 'number'],
                ['name' => 'attempts', 'label' => 'Allowed attempts', 'type' => 'number'],
                ['name' => 'instructions', 'label' => 'Instructions', 'type' => 'textarea'],
                ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => [
                    'draft' => 'Draft', 'published' => 'Published', 'closed' => 'Closed',
                ]],
            ],
        ];
    }
}
