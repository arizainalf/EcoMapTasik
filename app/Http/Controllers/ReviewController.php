<?php
namespace App\Http\Controllers;

use App\Models\Review;
use Illuminate\Http\Request;
use App\Traits\JsonResponder;

class ReviewController extends Controller
{
    use JsonResponder;
    public function index()
    {
        return view('pages.user.review.index');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'rating'     => 'required|numeric|min:1|max:5',
            'comment'    => 'nullable|string',
        ]);

        try{
            $review = Review::create([
                'user_id'    => auth()->user()->id,
                'product_id' => $validated['product_id'],
                'rating'     => $validated['rating'],
                'comment'    => $validated['comment'],
            ]);

            return $this->successResponse(
                $review,
                'Berhasil menambahkan ulasan.'
            );
        }catch(\Exception $e){
            return $this->errorResponse(
                $e->getMessage(),
                'Gagal menambahkan ulasan.'
            );
        }
    }
}
