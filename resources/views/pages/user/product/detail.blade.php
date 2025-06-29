@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <section class="py-5">
        <div class="container">

            <div class="row g-5">
                {{-- Gambar Produk --}}
                <div class="col-md-6">
                    <div class="border rounded-4 p-3 bg-light">
                        <img src="{{ Str::contains($product->image, 'http') ? $product->image : asset('storage/' . $product->image) }}"
                            alt="{{ $product->name }}" class="img-fluid rounded-3 w-100">
                    </div>
                </div>

                {{-- Detail Produk --}}
                <div class="col-md-6">
                    <h2 class="mb-3">{{ $product->name }}</h2>

                    <p class="text-muted mb-2">
                        <span class="badge bg-success">Diskon {{ getSetting()->discount }}%</span>
                    </p>

                    <div class="mb-3">
                        <span class="text-muted text-decoration-line-through">
                            {{ formatRupiah($product->price + ($product->price * getSetting()->discount) / 100) }}
                        </span>
                        <span class="fs-3 fw-bold text-primary">{{ formatRupiah($product->price) }}</span>
                    </div>

                    <div class="mb-3">
                        <span class="badge bg-primary">Stok: {{ $product->stock }} Pcs</span>
                        @php
                            $avg = $product->reviews->avg('rating');
                            $fullStars = floor($avg);
                            $halfStar = $avg - $fullStars >= 0.5;
                            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                        @endphp
                        <a class="text-decoration-none" href="{{ route('review.product', $product->id) }}">
                            <span class="text-primary">
                                @for ($i = 0; $i < $fullStars; $i++)
                                    <i class="fa fa-star"></i>
                                @endfor
                                @if ($halfStar)
                                    <i class="fa fa-star-half-alt"></i>
                                @endif
                                @for ($i = 0; $i < $emptyStars; $i++)
                                    <i class="fa fa-star-o"></i>
                                @endfor
                            </span>
                        </a>
                        <span>({{ number_format($avg, 1) }} / 5.0)</span>
                    </div>
                    <div class="mb-4">
                        <h5>Deskripsi</h5>
                        <p class="text-muted">{{ $product->description }}</p>
                    </div>

                    <form class="add-to-cart-form" data-id="{{ $product->id }}">
                        <input type="hidden" name="product_id" value="{{ $product->id }}">
                        <input type="hidden" name="cart_id" value="{{ getCartId() }}">
                        <div class="d-flex justify-content-between gap-3">
                            <div class="input-group product-qty">
                                <button type="button" class="btn btn-outline-danger quantity-left-minus btn-number"
                                    data-type="minus">-</button>
                                <input type="text" id="quantity" name="quantity"
                                    class="form-control text-center input-number" value="1">
                                <button type="button" class="btn btn-outline-success quantity-right-plus btn-number"
                                    data-type="plus">+</button>
                            </div>
                            <button type="submit" class="btn btn-lg btn-primary">
                                <i class="fa-solid fa-cart-plus me-2"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            {{-- Produk Terkait --}}
            <div class="mt-5">
                <h4 class="mb-4">Produk Terkait</h4>
                <div class="row row-cols-2 row-cols-md-4 g-4">
                    @foreach ($products as $related)
                        <div class="col">
                            <div class="card h-100 border-0 shadow-sm">
                                <a href="{{ route('products.show', $related->id) }}">
                                    <img src="{{ Str::contains($related->image, 'http') ? $related->image : asset('storage/' . $related->image) }}"
                                        class="card-img-top rounded-2" alt="{{ $related->name }}">
                                </a>
                                <div class="card-body p-2">
                                    <h6 class="card-title text-truncate">{{ $related->name }}</h6>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="text-muted small text-decoration-line-through">
                                            {{ formatRupiah($related->price + ($related->price * getSetting()->discount) / 100) }}
                                        </span>
                                        <span class="fw-bold">{{ formatRupiah($related->price) }}</span>
                                    </div>
                                </div>
                                <div class="card-footer bg-transparent border-0">
                                    <a href="{{ route('products.show', $related->id) }}"
                                        class="btn btn-outline-primary btn-sm w-100">Detail</a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </section>
@endsection

@push('scripts')
    <script>
        $(document).ready(function() {
            $(document).on('submit', '.add-to-cart-form', function(e) {
                e.preventDefault();
                const url = '{{ route('cart.store') }}';
                const data = new FormData(this);
                const method = 'POST';

                const successCallback = function(response) {
                    successToast(response.message);
                    loadCartItems();
                }

                const errorCallback = function(error) {
                    errorToast('error', error);
                }

                ajaxCall(url, method, data, successCallback, errorCallback);
            });
        });
    </script>
@endpush
