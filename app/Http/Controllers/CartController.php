<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartProduct;
use App\Traits\JsonResponder;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use JsonResponder;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $cart = Cart::with('cartProducts.product')->where('user_id', auth()->user()->id)->first();

            $data = [
                'view' => view('components.user.cart', compact('cart'))->render(),
            ];

            return $this->successResponse($data, 'Data berhasil ditemukan.');
        }
        return redirect()->back();
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {

    }

    /**
     * Store a newly created resource in storage.
     */public function store(Request $request)
    {
        $validated = $request->validate([
            'cart_id'    => ['required', 'exists:carts,id'],
            'product_id' => ['required', 'exists:products,id'],
            'quantity'   => ['nullable', 'numeric'],
        ]);

        try {
            $validated['quantity'] = $request->quantity ?? 1;
            // Cek apakah produk sudah ada di keranjang
            $cartProduct = CartProduct::where('cart_id', $validated['cart_id'])
                ->where('product_id', $validated['product_id'])
                ->first();

            if ($cartProduct) {
                // Jika ada → update quantity
                $cartProduct->increment('quantity', $validated['quantity']);
            } else {
                // Jika belum ada → buat baru
                $cartProduct = CartProduct::create($validated);
            }

            return $this->successResponse(
                $cartProduct,
                'Berhasil menambahkan produk ke keranjang.'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(null, $e->getMessage());
        }
    }

    public function updateQuantity(Request $request, string $id)
    {
        // dd($request->all());
        $validated = $request->validate([
            'quantity' => ['nullable', 'numeric'],
        ]);

        $cartProduct = CartProduct::where('id', $id)->first();

        try {
            $cartProduct->update($validated);

            return $this->successResponse(
                $cartProduct,
                'Berhasil menambahkan quantity produk.'
            );
        } catch (\Exception $e) {
            return $this->errorResponse(null, $e->getMessage());
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    public function items()
    {
        $cartItems = CartProduct::where('cart_id', getCartId())->get(); // atau sesuai query cart user
        $total     = 0;

        return view('pages.user.components.cart', compact('cartItems', 'total'))->render();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        try {
            $cartProduct = CartProduct::findOrFail($id);
            $cartProduct->delete();

            return $this->successResponse(null, 'Produk berhasil dihapus dari keranjang.');
        } catch (\Exception $e) {
            return $this->errorResponse(null, $e->getMessage());
        }
    }

}
