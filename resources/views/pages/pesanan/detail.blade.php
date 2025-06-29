@extends('layouts.app')

@section('title', 'Detail Pesanan')

@push('styles')
    <style>
        .modal-dialog {
            position: relative;
            width: auto;
            margin: var(--bs-modal-margin);
            pointer-events: auto;
            /* ini penting */
        }

        .modal-dialog-centered {
            display: flex;
            align-items: center;
            min-height: calc(100% - var(--bs-modal-margin) * 2);
        }

        .mx-auto {
            margin-right: auto !important;
            margin-left: auto !important;
        }

        #reviewModal.show {
            display: flex !important;
            align-items: center;
            justify-content: center;
        }
    </style>
@endpush

@section('content')
    <div class="container-fluid my-4">

        <div class="row g-3 px-xl-5">
            <h4 class="font-weight-semi-bold mb-4">Pesanan {{ $order->invoice_number }}</h4>
            @forelse ($orderProducts as $item)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0 small">
                        @if ($item->product?->image)
                            <img src="{{ Str::contains($item->product->image, 'http') ? $item->product->image : asset('storage/' . $item->product->image) }}"
                                class="card-img-top" alt="{{ $item->product->name }}" style="object-fit: cover;">
                        @else
                            <div class="bg-light d-flex align-items-center justify-content-center" style="height: 160px;">
                                <span class="text-muted"><i class="fas fa-image me-1"></i> Tidak ada gambar</span>
                            </div>
                        @endif
                        <div class="card-body px-3 py-2">
                            <h6 class="card-title text-truncate mb-1" style="font-size: 0.9rem;">
                                {{ $item->product->name ?? 'Produk Tidak Ditemukan' }}
                            </h6>
                            <ul class="list-unstyled mb-2" style="font-size: 0.85rem;">
                                <li><i class="fas fa-box me-1 text-muted"></i><strong>Jumlah:</strong> {{ $item->quantity }}
                                </li>
                                <li><i class="fas fa-tags me-1 text-muted"></i><strong>Total:</strong>
                                    Rp{{ number_format($item->total_price, 2, ',', '.') }}</li>
                            </ul>
                            <button type="button" class="btn btn-sm btn-outline-primary w-100 btn-review"
                                data-product-id="{{ $item->product_id }}" data-order-id="{{ $item->order_id }}"
                                data-bs-toggle="modal" data-bs-target="#reviewModal">
                                <i class="fas fa-pen me-1"></i> Tulis Ulasan
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">Tidak ada produk dalam pesanan ini.</div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Modal Review -->
    <div class="modal fade" id="reviewModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static"
        data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered mx-auto">
            <form id="review-form">
                @csrf
                <input type="hidden" name="product_id" id="product_id">
                <input type="hidden" name="order_id" id="order_id">
                <input type="hidden" name="rating" id="rating-value">
                <div class="modal-content">
                    <div class="modal-header bg-success text-white">
                        <h5 class="modal-title" id="reviewModalLabel">Tulis Ulasan Produk</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                            aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Rating -->
                        <div class="mb-3">
                            <label class="form-label">Rating</label>
                            <div id="rating-stars" class="text-primary fs-4">
                                @for ($i = 1; $i <= 5; $i++)
                                    <i class="far fa-star star" data-value="{{ $i }}"
                                        style="cursor: pointer;"></i>
                                @endfor
                            </div>
                        </div>

                        <!-- Komentar -->
                        <div class="mb-3">
                            <label for="comment" class="form-label">Komentar</label>
                            <textarea name="comment" id="comment" rows="4" class="form-control" placeholder="Tulis ulasan Anda..."></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" class="btn btn-primary">Kirim Ulasan</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#rating-stars .star').on('click', function() {
                let rating = $(this).data('value');

                $('#rating-value').val(rating);

                $('#rating-stars .star').each(function() {
                    let starValue = $(this).data('value');
                    if (starValue <= rating) {
                        $(this).removeClass('far').addClass('fas');
                    } else {
                        $(this).removeClass('fas').addClass('far');
                    }
                });
            });

            $('.btn-review').on('click', function() {
                const productId = $(this).data('product-id');
                const orderId = $(this).data('order-id');
                $('#review-form').find('input[name="product_id"]').val(productId);
                $('#review-form').find('input[name="order_id"]').val(orderId);
            });

            $('#review-form').on('submit', function(e) {
                e.preventDefault();
                const url = '{{ route('review.store') }}';
                const data = new FormData(this);
                const method = 'POST';

                const successCallback = function(response) {
                    successToast(response.message);
                    $('#reviewModal').modal('hide');
                }

                const errorCallback = function(error) {
                    errorToast(error);
                }

                ajaxCall(url, method, data, successCallback, errorCallback);
            });

        });
    </script>
@endpush
