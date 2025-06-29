@extends('layouts.app')

@section('title', 'Pesanan')

@push('styles')
@endpush

@section('content')
    <div class="container-fluid mb-4">
        <div class="row g-3 px-xl-5">
            @forelse ($orders as $order)
                <div class="col-md-6 col-lg-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div
                            class="card-header bg-primary text-white d-flex justify-content-between align-items-center py-2">
                            <span><i class="fas fa-receipt me-1"></i>{{ $order->invoice_number }}</span>
                            <span class="badge bg-light text-primary ">
                                {{ humanize($order->status) }}
                            </span>
                        </div>
                        <div class="card-body small">
                            <ul class="list-unstyled mb-0">
                                <li><i class="fas fa-user me-1 text-muted"></i><strong>User:</strong>
                                    {{ $order->user->name ?? '-' }}</li>
                                <li><i class="fas fa-money-bill-wave me-1 text-muted"></i><strong>Total:</strong>
                                    Rp{{ number_format($order->total_price, 2, ',', '.') }}</li>
                                <li><i class="fas fa-university me-1 text-muted"></i><strong>Bank:</strong>
                                    {{ $order->bankAccount->bank_name ?? '-' }}</li>
                                <li>
                                    <i class="fas fa-file-invoice-dollar me-1 text-muted"></i><strong>Bukti Bayar:</strong>
                                    @if ($order->payment_proof)
                                        <a href="{{ asset('storage/' . $order->payment_proof) }}" target="_blank"
                                            class="text-decoration-underline">Lihat</a>
                                    @elseif($order->status == 'belum_dibayar')
                                        <form action="{{ route('orders.update.bukti', $order->id) }}"
                                            enctype="multipart/form-data" class="mt-2" id="upload-bukti">
                                            @csrf
                                            <input type="file" name="payment_proof" accept="image/*,application/pdf"
                                                class="form-control form-control-sm mb-2" required>
                                            <button type="submit" class="btn btn-sm btn-outline-success w-100">
                                                <i class="fas fa-upload me-1"></i>Upload Bukti
                                            </button>
                                        </form>
                                    @else
                                        <span>-</span>
                                    @endif
                                </li>
                                <li><i class="fas fa-calendar-alt me-1 text-muted"></i><strong>Tgl Bayar:</strong>
                                    {{ $order->paid_at ?? '-' }}</li>
                                <li><i class="fas fa-shipping-fast me-1 text-muted"></i><strong>Kurir:</strong>
                                    {{ humanize($order->courier) ?? '-' }}</li>
                                <li><i class="fas fa-barcode me-1 text-muted"></i><strong>No Resi:</strong>
                                    {{ $order->tracking_number ?? '-' }}</li>
                                <li><i class="fas fa-map-marker-alt me-1 text-muted"></i><strong>Alamat:</strong>
                                    {{ Str::limit($order->address->full_address ?? '-', 60) }}</li>
                            </ul>
                        </div>
                        <div class="card-footer bg-white text-end">
                            <a href="{{ route('orders.detail', $order->id) }}" class="btn btn-sm btn-outline-primary">
                                <i class="fas fa-eye me-1"></i> Detail
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12">
                    <div class="alert alert-info text-center">Belum ada pesanan.</div>
                </div>
            @endforelse
        </div>
    </div>
@endsection
@push('scripts')
    <script>
        $(document).ready(function() {
            $('#upload-bukti').submit(function(e) {
                e.preventDefault();
                const url = $(this).attr('action');
                const data = new FormData(this);
                data.append('_method', 'PUT');

                const successCallback = function(response) {
                    successToast(response.message);
                    location.reload();
                }

                const errorCallback = function(error) {
                    errorToast(error);
                }

                ajaxCall(url, "POST", data, successCallback, errorCallback);
            });
        })
    </script>
@endpush
