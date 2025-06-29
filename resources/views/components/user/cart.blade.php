@auth()
    @if (auth()->user()->role == 'user')
        <form action="{{ route('orders.order') }}" method="GET">
            <h4 class="d-flex justify-content-between align-items-center mb-3">
                <span class="text-primary">Keranjang Belanja</span>
                <span class="badge bg-primary rounded-pill">{{ getCartCount() }}</span>
            </h4>
            <ul class="list-group mb-3">
                <li class="list-group-item d-flex align-items-center">
                    <div class="form-check me-2">
                        <input class="form-check-input" type="checkbox" id="selectAllCart">
                        <label class="form-check-label fw-bold" for="selectAllCart">
                            Pilih Semua
                        </label>
                    </div>
                </li>
                @php
                    $totalPriceCart = 0;
                @endphp
                @foreach ($cart->cartProducts as $cart)
                    @php
                        $totalPriceProduct = $cart->product->price * $cart->quantity;
                        $totalPriceCart += $totalPriceProduct;
                    @endphp
                    <div class="list-group-item">
                        <li class="row lh-sm mb-3">
                            <div class="col-6 d-flex justify-content-between">
                                <div class="form-check me-2">
                                    <input class="form-check-input cart-item-checkbox" type="checkbox"
                                        name="orderProducts[]" value="{{ $cart->id }}"
                                        id="cartCheckbox{{ $cart->id }}">
                                    <label class="form-check-label" for="cartCheckbox{{ $cart->id }}">
                                        {{ $cart->product->name }}
                                    </label>
                                </div>
                            </div>
                            <div class="col-6 text-end">
                                <span class="text-body-secondary">{{ formatRupiah($cart->product->price) }}</span>
                            </div>
                        </li>
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="input-group cart-product-qty" data-id="{{ $cart->id }}">
                                <span class="input-group-btn">
                                    <button type="button" class="quantity-left-minus btn btn-danger btn-number"
                                        data-type="minus">
                                        <svg width="16" height="16">
                                            <use xlink:href="#minus"></use>
                                        </svg>
                                    </button>
                                </span>
                                <input type="text" name="quantity" id="cart-quantity" data-id="{{ $cart->id }}"
                                    class="form-control input-number cart-quantity" value="{{ $cart->quantity }}"
                                    style="width: 50px">
                                <span class="input-group-btn">
                                    <button type="button" class="quantity-right-plus btn btn-success btn-number"
                                        data-type="plus">
                                        <svg width="16" height="16">
                                            <use xlink:href="#plus"></use>
                                        </svg>
                                    </button>
                                </span>
                                <button onclick="deleteCartProduct('{{ $cart->id }}')" class="btn btn-danger mx-2">
                                    <i class="fas fa-trash"></i>
                                </button>
                                <span class="text-body-secondary">{{ formatRupiah($totalPriceProduct) }}</span>

                            </div>
                        </div>
                    </div>
                @endforeach
            </ul>
            <div class="d-flex justify-content-end my-2">
                {{ formatRupiah($totalPriceCart) }}
            </div>
            <button class="w-100 btn btn-primary btn-lg" type="submit">Lanjut Pesan</button>
        </form>
    @endif
@endauth
