@extends('layouts.app')
@section('title', 'Home')
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
@section('banner')
    {{-- <section class="py-3"
        style="background-image: url('{{ asset('FoodMart') }}/images/background-pattern.jpg');background-repeat: no-repeat;background-size: cover;">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="banner-blocks">

                        <div class="banner-ad large bg-info block-1">

                            <div class="swiper main-swiper">
                                <div class="swiper-wrapper">

                                    @foreach ($products as $product)
                                        <div class="swiper-slide">
                                            <div class="row banner-content p-5">
                                                <div class="content-wrapper col-md-7">
                                                    <div class="categories my-3">{{ $product->category->name }}</div>
                                                    <h3 class="display-4">{{ $product->name }}</h3>
                                                    <p>{{ $product->description }}</p>
                                                    <a href="{{ route('products.show', $product->id) }}"
                                                        class="btn btn-outline-dark btn-lg text-uppercase fs-6 rounded-1 px-4 py-3 mt-3">Beli</a>
                                                </div>
                                                <div class="img-wrapper col-md-5">
                                                    <img src="{{ Str::contains($product->image, 'http') ? $product->image : asset('storage/' . $product->image) }}"
                                                        class="img-fluid rounded-4" width="300px">
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="swiper-pagination"></div>

                            </div>
                        </div>

                        <div class="banner-ad bg-success-subtle block-2"
                            style="background:url('{{ asset('storage/' . getSetting()->slider_1) }}') no-repeat;background-position: right bottom">
                            <div class="row banner-content p-5">

                                <div class="content-wrapper col-md-7">
                                    <div class="categories sale mb-3 pb-3">20% off</div>
                                    <h3 class="banner-title">Fruits & Vegetables</h3>
                                    <a href="#" class="d-flex align-items-center nav-link">Shop Collection <svg
                                            width="24" height="24">
                                            <use xlink:href="#arrow-right"></use>
                                        </svg></a>
                                </div>

                            </div>
                        </div>

                        <div class="banner-ad bg-danger block-3"
                            style="background:url('{{ asset('storage/' . getSetting()->slider_1) }}') no-repeat;background-position: right bottom">
                            <div class="row banner-content p-5">

                                <div class="content-wrapper col-md-7">
                                    <div class="categories sale mb-3 pb-3">15% off</div>
                                    <h3 class="item-title">Baked Products</h3>
                                    <a href="#" class="d-flex align-items-center nav-link">Shop Collection <svg
                                            width="24" height="24">
                                            <use xlink:href="#arrow-right"></use>
                                        </svg></a>
                                </div>

                            </div>
                        </div>

                    </div>
                    <!-- / Banner Blocks -->

                </div>
            </div>
        </div>
    </section> --}}

    <section class="py-5 overflow-hidden">
        <div class="container-fluid">
            <div class="row">
                <div class="col-md-12">

                    <div class="section-header d-flex flex-wrap justify-content-between mb-5">
                        <h2 class="section-title">Kategori</h2>

                        <div class="d-flex align-items-center">
                            {{-- <a href="#" class="btn-link text-decoration-none">Lihat Semua</a> --}}
                            <div class="swiper-buttons">
                                <button class="swiper-prev category-carousel-prev btn btn-yellow">❮</button>
                                <button class="swiper-next category-carousel-next btn btn-yellow">❯</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="category-carousel swiper">
                        <div class="swiper-wrapper">
                            @foreach (getCategory() as $category)
                                <a data-id="{{ $category->id }}" class="nav-link category-item swiper-slide">
                                    <img src="{{ Str::contains($category->image, 'http') ? $category->image : asset('storage/' . $category->image) }}"
                                        alt="Category Thumbnail" class="rounded-2 category-thumbnail">
                                    <h3 class="category-title">{{ $category->name }}</h3>
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
@endsection
@section('content')

    <section class="py-3">
        <div class="container-fluid">

            <div class="row">
                <div class="col-md-12">

                    <div class="bootstrap-tabs product-tabs">
                        <div class="tabs-header d-flex justify-content-between border-bottom my-5">
                            <h3>Produk Terbaru</h3>
                        </div>
                        <div class="tab-content" id="nav-tabContent">
                            <div class="tab-pane fade show active" id="nav-all" role="tabpanel"
                                aria-labelledby="nav-all-tab">

                                <div
                                    class="product-grid row row-cols-1 row-cols-sm-2 row-cols-md-3 row-cols-lg-4 row-cols-xl-5">

                                    @foreach (getProducts() as $product)
                                        <div class="col">
                                            <div class="product-item">
                                                <span class="badge bg-success position-absolute m-3">-30%</span>

                                                <figure>
                                                    <a href="{{ route('products.show', $product->id) }}"
                                                        title="Product Title">
                                                        <img src="{{ Str::contains($product->image, 'http') ? $product->image : asset('storage/' . $product->image) }}"
                                                            class="tab-image" width="100%">
                                                    </a>
                                                </figure>
                                                <h3>{{ $product->name }}</h3>
                                                <span class="qty">1 Unit</span><span class="rating"><svg width="24"
                                                        height="24" class="text-primary">
                                                        <use xlink:href="#star-solid"></use>
                                                    </svg> 4.5</span>
                                                <span class="price">{{ formatRupiah($product->price) }}</span>
                                                <div class="d-flex align-items-center justify-content-between">
                                                    <form class="add-to-cart-form" data-id="{{ $product->id }}">
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
                                                        <button type="submit" class="nav-link add-to-cart">Add to Cart
                                                            <iconify-icon icon="uil:shopping-cart"></a>
                                                    </form>
                                                </div>
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
                    successToast('success', response);
                }

                const errorCallback = function(error) {
                    errorToast('error', error);
                }

                ajaxCall(url, method, data, successCallback, errorCallback);

            })
        })
    </script>
@endpush
