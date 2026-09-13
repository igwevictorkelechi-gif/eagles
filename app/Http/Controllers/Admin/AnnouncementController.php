<?php

namespace App\Http\Controllers\Admin;

use App\Models\Announcement;

class AnnouncementController extends ResourceController
{
    protected function config(): array
    {
        return [
            'model' => Announcement::class,
            'title' => 'Announcements',
            'singular' => 'Announcement',
            'subtitle' => 'Broadcast news to your school community.',
            'route' => 'app.announcements',
            'columns' => [
                ['key' => 'title', 'label' => 'Title'],
                ['key' => 'audience', 'label' => 'Audience'],
                ['label' => 'Date', 'value' => fn ($r) => optional($r->created_at)->format('M j, Y')],
            ],
            'fields' => [
                ['name' => 'title', 'label' => 'Title', 'required' => true],
                ['name' => 'body', 'label' => 'Message', 'type' => 'textarea', 'required' => true],
                ['name' => 'audience', 'label' => 'Audience', 'type' => 'select', 'options' => [
                    'all' => 'Everyone', 'students' => 'Students', 'teachers' => 'Teachers',
                    'parents' => 'Parents', 'staff' => 'Staff',
                ]],
            ],
        ];
    }
}
