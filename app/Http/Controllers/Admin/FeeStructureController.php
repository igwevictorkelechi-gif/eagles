<?php

namespace App\Http\Controllers\Admin;

use App\Models\FeeStructure;

class FeeStructureController extends ResourceController
{
    protected function config(): array
    {
        return [
            'model' => FeeStructure::class,
            'title' => 'Fee Structures',
            'singular' => 'Fee',
            'subtitle' => 'Define tuition and other fees.',
            'route' => 'app.fees',
            'columns' => [
                ['key' => 'name', 'label' => 'Fee'],
                ['key' => 'category', 'label' => 'Category'],
                ['key' => 'amount', 'label' => 'Amount', 'money' => true],
            ],
            'fields' => [
                ['name' => 'name', 'label' => 'Fee name', 'required' => true],
                ['name' => 'category', 'label' => 'Category', 'type' => 'select', 'options' => [
                    'tuition' => 'Tuition', 'registration' => 'Registration', 'examination' => 'Examination',
                    'transport' => 'Transport', 'boarding' => 'Boarding', 'uniform' => 'Uniform', 'miscellaneous' => 'Miscellaneous',
                ]],
                ['name' => 'amount', 'label' => 'Amount (NGN)', 'type' => 'number', 'required' => true],
            ],
        ];
    }
}
