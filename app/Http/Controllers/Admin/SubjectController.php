<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subject;

class SubjectController extends ResourceController
{
    protected function config(): array
    {
        return [
            'model' => Subject::class,
            'title' => 'Subjects',
            'singular' => 'Subject',
            'subtitle' => 'Manage the subjects offered.',
            'route' => 'app.subjects',
            'orderBy' => 'name', 'orderDir' => 'asc',
            'columns' => [
                ['key' => 'name', 'label' => 'Subject'],
                ['key' => 'code', 'label' => 'Code'],
            ],
            'fields' => [
                ['name' => 'name', 'label' => 'Subject name', 'required' => true],
                ['name' => 'code', 'label' => 'Code'],
            ],
        ];
    }
}
