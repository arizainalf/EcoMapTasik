@extends('layouts.app')

@section('title', 'Checkout')

@push('styles')
@endpush

@section('content')
    <div class="container-fluid">
        <form id="checkoutForm">
            @csrf
            <div class="row px-xl-5">
                <!-- Billing Address -->
                <div class="col-lg-8">
                    <div class="mb-4">
                        <h4 class="font-weight-semi-bold mb-4">Billing Address</h4>
                        <div class="row g-3">
                            <input type="hidden" value="{{ auth()->user()->id }}" name="user_id">

                            <div class="col-md-6">
                                <label for="first_name" class="form-label">Nama Lengkap</label>
                                <input id="first_name" name="first_name" value="{{ auth()->user()->name }}"
                                    class="form-control" type="text" placeholder="John" required disabled>
                            </div>

                            <div class="col-md-6">
                                <label for="email" class="form-label">E-mail</label>
                                <input id="email" name="email" value="{{ auth()->user()->email }}"
                                    class="form-control" type="email" placeholder="example@email.com" required disabled>
                            </div>

                            <div class="col-md-6">
                                <label for="phone" class="form-label">No HP</label>
                                <input id="phone" name="phone" value="{{ getAddress()->phone_number ?? '' }}"
                                    class="form-control" type="text" placeholder="+123 456 789" required disabled>
                            </div>

                            <input type="hidden" value="{{ getAddress()->id }}" name="address_id">

                            <div class="col-md-6">
                                <label for="provinsi" class="form-label">Provinsi</label>
                                <input id="provinsi" type="text" value="{{ getAddress()->province }}"
                                    class="form-control" required disabled>
                            </div>

                            <div class="col-md-6">
                                <label for="kota" class="form-label">Kota</label>
                                <input id="kota" class="form-control" disabled required
                                    value="{{ getAddress()->city }}">
                            </div>

                            <div class="col-md-6">
                                <label for="kecamatan" class="form-label">Kecamatan</label>
                                <input id="kecamatan" class="form-control" disabled required
                                    value="{{ getAddress()->district }}">
                            </div>

                            <div class="col-md-6">
                                <label for="kelurahan" class="form-label">Kabupaten</label>
                                <input id="kelurahan" class="form-control" disabled required
                                    value="{{ getAddress()->subdistrict }}">
                            </div>

                            <div class="col-md-6">
                                <label for="kode_pos" class="form-label">Kode Pos</label>
                                <select name="postal_code" id="kode_pos" class="form-select" required>
                                    <option selected disabled>Pilih Kode Pos</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="alamat" class="form-label">Alamat Lengkap</label>
                                <textarea id="alamat" name="alamat" class="form-control" rows="3"
                                    placeholder="Contoh: Jl. Raya Tawang No. 123, RT 01/RW 05" required disabled>{{ getAddress()->full_address }}</textarea>
                            </div>

                            <div class="col-12">
                                <label for="kurir" class="form-label">Kurir</label>
                                <select name="courier" id="kurir" class="form-select" required>
                                    <option value="">Pilih Kurir</option>
                                    <option value="jne">JNE</option>
                                    <option value="jnt">JNT</option>
                                    <option value="sicepat">Sicepat</option>
                                </select>
                            </div>

                            <div class="col-12">
                                <label for="ongkir" class="form-label">Harga Ongkir</label>
                                <select name="ongkir" id="ongkir" class="form-select" disabled required>
                                </select>
                            </div>
                        </div>

                    </div>
                </div>

                <!-- Order Summary & Payment -->
                <div class="col-lg-4">
                    <div class="card border-success mb-4">
                        <div class="card-header bg-success border-0">
                            <h4 class="font-weight-semi-bold m-0">Ringkasan Order</h4>
                        </div>
                        <div class="card-body">
                            {{-- Example dynamic product summary --}}

                            @php
                                $total = 0;
                                $totalWeight = 0;
                            @endphp
                            @foreach ($orders as $index => $item)
                                @php
                                    $total += $item->product->price * $item->quantity;
                                    $totalWeight += $item->product->weight * $item->quantity;
                                @endphp
                                <input type="hidden" name="products_id[{{ $index }}][id]"
                                    value="{{ $item->product->id }}">
                                <input type="hidden" name="products_id[{{ $index }}][quantity]"
                                    value="{{ $item->quantity }}">
                                <input type="hidden" name="products_id[{{ $index }}][total_price]"
                                    value="{{ $item->product->price * $item->quantity }}">

                                <div class="d-flex justify-content-between">
                                    <p>{{ $item->product->name }}</p>
                                    <p>Rp {{ number_format($item->product->price * $item->quantity, 0, ',', '.') }}</p>
                                </div>
                            @endforeach
                            <hr class="mt-0">
                            <div class="d-flex justify-content-between mb-3 pt-1">
                                <h6 class="font-weight-medium">Subtotal</h6>
                                <h6 class="font-weight-medium">Rp
                                    {{ number_format($total, 0, ',', '.') }}</h6>
                            </div>
                            <div class="d-flex justify-content-between">
                                <h6 class="font-weight-medium">Shipping</h6>
                                <h6 class="font-weight-medium shipping-fee"></h6>
                            </div>
                        </div>
                        <div class="card-footer border-success bg-transparent">
                            <div class="d-flex justify-content-between mt-2">
                                <h5 class="font-weight-bold">Total</h5>
                                <h5 class="font-weight-bold total-price">Rp {{ number_format($total, 0, ',', '.') }}</h5>
                            </div>
                        </div>
                    </div>

                    <div class="card border-success mb-4">
                        <div class="card-header bg-success border-0">
                            <h4 class="font-weight-semi-bold m-0">Metode Pembayaran</h4>
                        </div>
                        <div class="card-body">
                            @foreach (getBanks() as $index => $bank)
                                <div class="form-check mb-2">
                                    <input class="form-check-input" type="radio" name="bank_account_id"
                                        id="bank{{ $bank->id }}" value="{{ $bank->id }}"
                                        {{ $index === 0 ? 'checked' : '' }} required>
                                    <label class="form-check-label" for="bank{{ $bank->id }}">
                                        {{ $bank->bank_name }} - {{ $bank->account_number }} -
                                        {{ $bank->account_holder }}
                                    </label>
                                </div>
                            @endforeach

                            <div class="form-group mt-3">
                                <label for="payment_proof">Bukti Pembayaran (optional)</label>
                                <input type="file" class="form-control-file" name="payment_proof" id="payment_proof"
                                    accept="image/*">
                            </div>
                        </div>
                        <div class="card-footer border-success bg-transparent">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary font-weight-bold">Pesan Sekarang</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
@endsection
@push('scripts')
    <script>
        function formatRp(angka) {
            var rev = parseInt(angka, 10).toString().split('').reverse().join('');
            var rev2 = '';
            for (var i = 0; i < rev.length; i++) {
                rev2 += rev[i];
                if ((i + 1) % 3 === 0 && i !== (rev.length - 1)) {
                    rev2 += '.';
                }
            }
            return rev2.split('').reverse().join('');
        }

        $(document).ready(function() {
            // 1. Load Provinsi dulu, lalu set dan trigger kota
            let provinsi = '{{ getAddress()->province }}';
            let kota = '{{ getAddress()->city }}';
            let kecamatan = '{{ getAddress()->district }}';
            let kelurahan = '{{ getAddress()->subdistrict }}';
            let kode_pos = '{{ getAddress()->postal_code }}';
            let kurir = $('#kurir').val();
            let weight = '{{ $totalWeight }}';
            let origin = '{{ getSetting()->postal_code }}';
            let totalWithOngkir;

            const search = `${provinsi} ${kota} ${kecamatan} ${kelurahan}`
            loadSelectOptions('#kode_pos', `/wilayah/tujuan?search=${search}`, kode_pos)

            $('#kode_pos').on('change', function() {
                kode_pos = $(this).val()
            })

            $('#kurir').on('change', function() {
                kurir = $(this).val()
                $('#ongkir').removeProp('disabled')
                loadSelectOptions('#ongkir',
                    `/wilayah/ongkir?origin=${origin}&destination=${kode_pos}&courier=${kurir}&weight=${weight}`
                )
            })

            $('#ongkir').on('change', function() {
                const value = $('#ongkir option:selected').text()
                const ongkir = value.split(' - ')[0];
                const total = '{{ $total }}';
                totalWithOngkir = parseInt(total) + parseInt(ongkir);
                $('.shipping-fee').text(`Rp ${formatRp(ongkir)}`)
                $('.total-price').text(`Rp ${formatRp(totalWithOngkir)}`)
                $('#total-price').val(totalWithOngkir)
                console.log(totalWithOngkir, total, ongkir, weight, origin, kode_pos, kurir,
                    'ongkir change')
            })

            $('#checkoutForm').on('submit', function(e) {
                e.preventDefault()
                $(this).find('button[type="submit"]').attr('disabled', true)
                $(this).find('button[type="submit"]').html('Please Wait...')

                const formData = new FormData(this)
                formData.append('total_price', totalWithOngkir)

                const url = "{{ route('orders.store') }}"

                const successCallback = function(response) {
                    handleSuccess(response)
                }

                const errorCallback = function(error) {
                    handleSimpleError(error)
                }

                for (let pair of formData.entries()) {
                    console.log(`${pair[0]}: ${pair[1]}`);
                }

                ajaxCall(url, 'POST', formData, successCallback, errorCallback)
            })


        });
    </script>
@endpush
