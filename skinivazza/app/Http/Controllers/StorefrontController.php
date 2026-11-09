<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\View\View;

class StorefrontController extends Controller
{
    public function index(): View
    {
        return view('storefront.index', [
            'categories' => Category::query()->where('is_active', true)->orderBy('name')->get(),
            'products' => Product::query()
                ->with(['category', 'images'])
                ->where('is_active', true)
                ->latest()
                ->paginate(12),
        ]);
    }
}
