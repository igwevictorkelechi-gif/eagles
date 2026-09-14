<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;

class ProductController extends ResourceController
{
    protected function config(): array
    {
        return [
            'model' => Product::class,
            'title' => 'Products & Inventory',
            'singular' => 'Product',
            'subtitle' => 'Manage sellable materials and stock.',
            'route' => 'app.inventory',
            'orderBy' => 'name', 'orderDir' => 'asc',
            'columns' => [
                ['key' => 'name', 'label' => 'Product'],
                ['key' => 'category', 'label' => 'Category'],
                ['key' => 'selling_price', 'label' => 'Price', 'money' => true],
                ['label' => 'Stock', 'value' => fn ($r) => $r->stock_qty <= $r->low_stock ? $r->stock_qty . ' ⚠' : $r->stock_qty],
            ],
            'fields' => [
                ['name' => 'name', 'label' => 'Product name', 'required' => true],
                ['name' => 'sku', 'label' => 'SKU'],
                ['name' => 'category', 'label' => 'Category'],
                ['name' => 'purchase_price', 'label' => 'Purchase price (NGN)', 'type' => 'number'],
                ['name' => 'selling_price', 'label' => 'Selling price (NGN)', 'type' => 'number', 'required' => true],
                ['name' => 'stock_qty', 'label' => 'Stock quantity', 'type' => 'number'],
                ['name' => 'low_stock', 'label' => 'Low-stock threshold', 'type' => 'number'],
            ],
        ];
    }
}
