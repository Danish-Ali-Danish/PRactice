<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Latest 8 products
        $latestProducts = Product::latest()->take(8)->get();

        // You can add featured logic if needed
        $featuredProducts = Product::inRandomOrder()->take(4)->get();

        return view('user.home', compact('latestProducts', 'featuredProducts'));
    }
}
