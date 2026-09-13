<?php

namespace App\Http\Controllers\Admin;

use App\Models\Transaction;

class AccountingController extends ResourceController
{
    protected function config(): array
    {
        return [
            'model' => Transaction::class,
            'title' => 'Accounting',
            'singular' => 'Transaction',
            'subtitle' => 'Record income and expenses.',
            'route' => 'app.accounting',
            'columns' => [
                ['key' => 'type', 'label' => 'Type', 'badge' => true],
                ['key' => 'category', 'label' => 'Category'],
                ['key' => 'description', 'label' => 'Description'],
                ['key' => 'amount', 'label' => 'Amount', 'money' => true],
                ['key' => 'date', 'label' => 'Date'],
            ],
            'fields' => [
                ['name' => 'type', 'label' => 'Type', 'type' => 'select', 'required' => true, 'options' => ['income' => 'Income', 'expense' => 'Expense']],
                ['name' => 'category', 'label' => 'Category'],
                ['name' => 'description', 'label' => 'Description'],
                ['name' => 'amount', 'label' => 'Amount (NGN)', 'type' => 'number', 'required' => true],
                ['name' => 'date', 'label' => 'Date', 'type' => 'date'],
            ],
        ];
    }
}
