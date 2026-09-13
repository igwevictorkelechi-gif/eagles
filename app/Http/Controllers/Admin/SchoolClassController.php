<?php

namespace App\Http\Controllers\Admin;

use App\Models\SchoolClass;
use App\Models\Teacher;

class SchoolClassController extends ResourceController
{
    protected function config(): array
    {
        $teachers = Teacher::orderBy('first_name')
            ->get()
            ->mapWithKeys(fn ($t) => [$t->id => trim($t->first_name . ' ' . $t->last_name)])
            ->all();
        return [
            'model' => SchoolClass::class,
            'title' => 'Classes',
            'singular' => 'Class',
            'subtitle' => 'Set up classes and assign form teachers.',
            'route' => 'app.classes',
            'orderBy' => 'name',
            'orderDir' => 'asc',
            'columns' => [
                ['key' => 'name', 'label' => 'Class'],
                ['key' => 'level', 'label' => 'Level'],
                ['label' => 'Form teacher', 'value' => fn ($r) => $teachers[$r->teacher_id] ?? '—'],
                ['key' => 'capacity', 'label' => 'Capacity'],
            ],
            'fields' => [
                ['name' => 'name', 'label' => 'Class name', 'required' => true],
                ['name' => 'level', 'label' => 'Level'],
                ['name' => 'teacher_id', 'label' => 'Form teacher', 'type' => 'select', 'options' => $teachers],
                ['name' => 'capacity', 'label' => 'Capacity', 'type' => 'number'],
            ],
        ];
    }
}
