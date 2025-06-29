@extends('layouts.app')

@section('title', $product->name)

@section('content')
    <div class="container py-5">
        <div class="row">
            <div class="col-md-6 mb-4">
                <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded">
            </div>
            <div class="col-md-6 mb-4">
                <h2>{{ $product->name }}</h2>
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

        <hr class="my-5">

        <div class="row d-flex justify-content-center">
            <div class="col-12">
                <h4>Ulasan Produk</h4>
                @foreach ($product->reviews as $review)
                    <div class="card mb-3">
                        <div class="card-body">
                            <div class="d-flex justify-content-between">
                                <strong>{{ $review->user->name }}</strong>
                                <span class="text-primary">
                                    @for ($i = 0; $i < $review->rating; $i++)
                                        <i class="fas fa-star"></i>
                                    @endfor
                                </span>
                            </div>
                            <p class="mb-0">{{ $review->comment }}</p>
                            <small class="text-muted">{{ $review->created_at->diffForHumans() }}</small>
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- <div class="col-md-4">
                @auth
                    <h4>Tambah Ulasan</h4>
                    <form action="{{ route('review.store', $product) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label for="rating" class="form-label">Rating</label>
                            <select name="rating" id="rating" class="form-select" required>
                                <option value="">Pilih Rating</option>
                                @for ($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} ★</option>
                                @endfor
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="comment" class="form-label">Komentar</label>
                            <textarea name="comment" id="comment" class="form-control" rows="3"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Kirim Review</button>
                    </form>
                @else
                    <p>Silakan <a href="{{ route('login') }}">login</a> untuk menulis review.</p>
                @endauth
            </div> --}}
        </div>
    </div>
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
