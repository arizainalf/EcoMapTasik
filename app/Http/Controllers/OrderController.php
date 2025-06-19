<?php
namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Order;
use App\Models\OrderProduct;
use App\Models\Product;
use App\Traits\JsonResponder;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    use JsonResponder;
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('pages.user.order.index');
    }
    public function order()
    {
        $orders = Order::with('bankAccount')->where('user_id', auth()->user()->id)->get();
        return view('pages.user.order.orders', compact('orders'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function detail(string $id)
    {
        $orderProducts = OrderProduct::with('product')->where('order_id', $id)->get();
        return view('pages.user.order.detail', compact('orderProducts'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'                   => 'required|exists:users,id',
            'total_price'               => 'required|numeric|min:0',
            'bank_account_id'           => 'nullable|exists:bank_accounts,id',
            'payment_proof'             => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'courier'                   => 'nullable|string|max:100',
            'address_id'                => 'required|exists:addresses,id',
            'products_id'               => 'required|array|min:1',
            'products_id.*.id'          => 'required|exists:products,id',
            'products_id.*.quantity'    => 'required|numeric|min:1',
            'products_id.*.total_price' => 'required|numeric|min:0',
        ]);

        try {
            if ($request->hasFile('payment_proof')) {
                // ✅ Simpan di storage/public supaya bisa diakses langsung
                $validated['payment_proof'] = $request->file('payment_proof')->store('payment_proofs', 'public');
                $validated['paid_at']       = now();
            }

            $order = Order::create($validated);

            foreach ($request->products_id as $item) {
                OrderProduct::create([
                    'order_id'    => $order->id,
                    'product_id'  => $item['id'],
                    'quantity'    => $item['quantity'],
                    'total_price' => $item['total_price'],
                ]);

                $product = Product::find($item['id']);
                $product->decrement('stock', $item['quantity']);
            }

            $cart = Cart::where('user_id', $request->user_id)->first();
            CartProduct::where('cart_id', $cart->id)->delete();

            return $this->successResponse('Order created successfully', $order);
        } catch (\Exception $e) {
            return $this->errorResponse(null, $e->getMessage(), 500);
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
    public function updateBukti(Request $request, string $id)
    {
        $order = Order::find($id);

        $validated = $request->validate([
            'payment_proof' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        try {
            if ($request->hasFile('payment_proof')) {
                // ✅ Simpan di storage/public supaya bisa diakses langsung
                $validated['payment_proof'] = $request->file('payment_proof')->store('payment_proofs', 'public');
                $validated['paid_at']       = now();
            }

            $order->update($validated);

            return $this->successResponse('Order updated successfully', $order);
        } catch (\Exception $e) {
            return $this->errorResponse(null, $e->getMessage(), 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
