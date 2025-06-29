@extends('layouts.app')
@section('title', $category->name)
@push('styles')
    <style>
        .category-thumbnail {
            width: 50px;
        }

        @media (min-width: 768px) {
            .category-thumbnail {
                width: 200px;
            }
        }
    </style>
@endpush
@section('content')
    <section class="">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">
                    <div class="bootstrap-tabs product-tabs">
                        <div class="tabs-header d-flex justify-content-between border-bottom my-5">
                            <h3>Kategori {{ $category->name }}</h3>
                        </div>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-all" role="tabpanel"
                                aria-labelledby="nav-all-tab">

                                <div
                                    class="product-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5">

                                    @foreach ($products as $product)
                                        <div class="col">
                                            <div class="product-item">
                                                <span
                                                    class="badge bg-success position-absolute m-3">-{{ getSetting()->discount }}%</span>

                                                <a href="{{ route('products.show', $product->id) }}" title="Product Title">
                                                    <img src="{{ Str::contains($product->image, 'http') ? $product->image : asset('storage/' . $product->image) }}"
                                                        class="img-fluid" height="100%" width="100%">
                                                </a>
                                                <h6>{{ $product->name }}</h6>
                                                <div>
                                                    <div class="my-auto">
                                                        @php
                                                            $avg = $product->reviews->avg('rating');
                                                            $fullStars = floor($avg);
                                                            $halfStar = $avg - $fullStars >= 0.5;
                                                            $emptyStars = 5 - $fullStars - ($halfStar ? 1 : 0);
                                                        @endphp
                                                        <a class="text-decoration-none"
                                                            href="{{ route('review.product', $product->id) }}">
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
                                                </div>
                                                <div class="text-end">
                                                    <small
                                                        class="text-muted text-decoration-line-through">{{ formatRupiah($product->price + ($product->price * getSetting()->discount) / 100) }}</small>
                                                </div>
                                                <div class="d-flex justify-content-between">
                                                    <span class="qty my-auto">{{ $product->stock }} Pcs</span>
                                                    <div>
                                                        <span class="price">{{ formatRupiah($product->price) }}</span>
                                                    </div>
                                                </div>
                                                <form class="add-to-cart-form" data-id="{{ $product->id }}">
                                                    <div class="d-flex align-items-center justify-content-between">
                                                        <div class="input-group product-qty">
                                                            <span class="input-group-btn">
                                                                <button type="button"
                                                                    class="quantity-left-minus btn btn-danger btn-number"
                                                                    data-type="minus">
                                                                    <svg width="16" height="16">
                                                                        <use xlink:href="#minus"></use>
                                                                    </svg>
                                                                </button>
                                                            </span>
                                                            <input type="text" id="quantity" name="quantity"
                                                                class="form-control input-number" value="1">
                                                            <input type="hidden" name="product_id"
                                                                value="{{ $product->id }}">
                                                            <input type="hidden" name="cart_id"
                                                                value="{{ getCartId() }}">
                                                            <div class="d-flex">
                                                                <span class="input-group-btn">
                                                                    <button type="button"
                                                                        class="quantity-right-plus btn btn-success btn-number"
                                                                        data-type="plus">
                                                                        <svg width="16" height="16">
                                                                            <use xlink:href="#plus"></use>
                                                                        </svg>
                                                                    </button>
                                                                </span>

                                                            </div>
                                                        </div>
                                                        <button type="submit" class="add-to-cart btn btn-lg">
                                                            <i class="fa-solid fa-cart-plus"></i>
                                                        </button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    @endforeach

                                </div>
                                <!-- / product-grid -->

                            </div>
                        </div>
                    </div>

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
                    loadCartItems()
                }

                const errorCallback = function(error) {
                    errorToast('error', error);
                }

                ajaxCall(url, method, data, successCallback, errorCallback);

            })
        })
    </script>
@endpush
