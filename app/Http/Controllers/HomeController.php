<?php
namespace App\Http\Controllers;

use App\Models\Product;

class HomeController extends Controller
{
    public function index()
    {
        $products = Product::take(3)->get();
        return view('pages.user.home.index', compact('products'));
    }
}
