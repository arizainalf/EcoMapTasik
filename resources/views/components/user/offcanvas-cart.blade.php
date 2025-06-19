    <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasCart" aria-labelledby="My Cart">
        <div class="offcanvas-header justify-content-center">
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="order-md-last">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-primary">Keranjang Belanja</span>
                    <span class="badge bg-primary rounded-pill">{{ getCartCount() }}</span>
                </h4>
                <ul class="list-group mb-3">
                    @php
                        $totalPriceCart = 0;
                    @endphp
                    @foreach (getCart()->cartProducts as $cart)
                        @php
                            $totalPriceCart += $cart->product->price;
                        @endphp
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div>
                                <h6 class="my-0">{{ $cart->product->name }}</h6>
                                <small class="text-body-secondary">{{ $cart->product->desription }}</small>
                            </div>
                            <span class="text-body-secondary">{{ formatRupiah($cart->product->price) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between lh-sm">
                            <div>
                                <h6 class="my-0">{{ $cart->product->name }}</h6>
                                <small class="text-body-secondary">{{ $cart->product->desription }}</small>
                            </div>
                            <span class="text-body-secondary">{{ formatRupiah($cart->product->price) }}</span>
                        </li>
                    @endforeach
                </ul>
                <div class="d-flex justify-content-end my-2">
                    {{ formatRupiah($totalPriceCart) }}
                </div>
                <button class="w-100 btn btn-primary btn-lg" type="submit">Lanjut Pesan</button>
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" data-bs-scroll="true" tabindex="-1" id="offcanvasSearch"
        aria-labelledby="Search">
        <div class="offcanvas-header justify-content-center">
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="order-md-last">
                <h4 class="d-flex justify-content-between align-items-center mb-3">
                    <span class="text-primary">Search</span>
                </h4>
                <form role="search" action="index.html" method="get" class="d-flex mt-3 gap-0">
                    <input class="form-control rounded-start rounded-0 bg-light" type="email"
                        placeholder="What are you looking for?" aria-label="What are you looking for?">
                    <button class="btn btn-dark rounded-end rounded-0" type="submit">Search</button>
                </form>
            </div>
        </div>
    </div>
