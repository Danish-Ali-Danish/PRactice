<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        // Load filters
        $categories = Category::all();
        $brands = Brand::all();

        // Filtered Products Query
        $products = Product::query();

        if ($request->category) {
            $products->where('category_id', $request->category);
        }

        if ($request->brand) {
            $products->where('brand_id', $request->brand);
        }

        if ($request->search) {
            $products->where('name', 'like', '%' . $request->search . '%');
        }

        // Get paginated or full results
        $products = $products->latest()->paginate(12);

        return view('user.products.index', compact('products', 'categories', 'brands'));
    }
}
