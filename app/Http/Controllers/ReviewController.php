<?php
namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use App\Traits\JsonResponder;
use Illuminate\Http\Request;

class ReviewController extends Controller
{
    use JsonResponder;
    public function index(string $id)
    {
        $product = Product::with('reviews')->where('id', $id)->first();
        return view('pages.user.product.review', compact('product'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_id'   => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'rating'     => 'required|numeric|min:1|max:5',
            'comment'    => 'nullable|string',
        ]);

        try {
            $review = Review::create([
                'user_id'    => auth()->user()->id,
                'product_id' => $validated['product_id'],
                'order_id'   => $validated['order_id'],
                'rating'     => $validated['rating'],
                'comment'    => $validated['comment'],
            ]);

            return $this->successResponse(
                $review,
                'Berhasil menambahkan ulasan.'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                'Gagal menambahkan ulasan.'
            );
        }
    }
}
