<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $product = Product::with('category', 'reviews', 'reviews.user')->where('id', $id)->firstOrFail();

        $products = Product::with('category')
            ->where('id', '!=', $product->id)
            ->latest()
            ->take(5)
            ->get();

        return view('pages.user.product.detail', compact('product', 'products'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function category(string $id)
    {
        $category = Category::where('id', $id)->firstOrFail();
        $products = Product::where('category_id', $category->id)->latest()->get();
        return view('pages.user.product.category', compact('category', 'products'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function reviews(string $id)
    {
        $product = Product::with('reviews')->where('id', $id)->first();
        return view('pages.user.product.review', compact('product'));
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        //
    }
}
