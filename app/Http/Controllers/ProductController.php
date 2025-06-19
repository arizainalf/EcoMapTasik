<?php
namespace App\Http\Controllers;

use App\Models\Product;
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
        $product = Product::with('category', 'reviews','reviews.user')->where('id', $id)->firstOrFail();

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
    public function edit(string $slug)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $slug)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $slug)
    {
        //
    }
}
