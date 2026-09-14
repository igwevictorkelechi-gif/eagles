<?php

namespace App\Http\Controllers\Admin;

use App\Models\Teacher;

class TeacherController extends ResourceController
{
    protected function config(): array
    {
        return [
            'model' => Teacher::class,
            'title' => 'Teachers',
            'singular' => 'Teacher',
            'subtitle' => 'Manage teaching staff.',
            'route' => 'app.teachers',
            'columns' => [
                ['label' => 'Name', 'value' => fn ($r) => trim($r->first_name . ' ' . $r->last_name)],
                ['key' => 'email', 'label' => 'Email'],
                ['key' => 'subject', 'label' => 'Subject'],
                ['key' => 'employee_no', 'label' => 'Employee No'],
                ['key' => 'status', 'label' => 'Status', 'badge' => true],
            ],
            'fields' => [
                ['name' => 'first_name', 'label' => 'First name', 'required' => true],
                ['name' => 'last_name', 'label' => 'Last name'],
                ['name' => 'email', 'label' => 'Email'],
                ['name' => 'phone', 'label' => 'Phone'],
                ['name' => 'subject', 'label' => 'Subject'],
                ['name' => 'employee_no', 'label' => 'Employee No'],
                ['name' => 'status', 'label' => 'Status', 'type' => 'select', 'options' => ['active' => 'Active', 'inactive' => 'Inactive']],
            ],
        ];
    }
}
