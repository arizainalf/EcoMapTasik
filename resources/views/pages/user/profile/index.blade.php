  @extends('layouts.app')
  @section('title', 'Profile')
  @section('content')
      <section class="py-2">
          <div class="container">
              <h2 class="text-center mb-5">Profile</h2>
              {{-- Card 1: Identitas --}}
              <div class="col-12 mb-4">
                  <div class="card shadow-sm border-0">
                      <form id="updateIdentitas">
                          <div class="card-header bg-primary text-white">
                              <h5 class="mb-0">Identitas</h5>
                          </div>
                          <div class="card-body">
                              <div class="row g-3">
                                  <div class="col-md-6">
                                      <label for="email" class="form-label">Email</label>
                                      <input type="email" name="email" id="email" value="{{ $user->email }}"
                                          class="form-control" required>
                                  </div>
                                  <div class="col-md-6">
                                      <label for="name" class="form-label">Nama Lengkap</label>
                                      <input type="text" id="name" name="name" value="{{ $user->name }}"
                                          class="form-control" required>
                                  </div>
                                  <div class="col-md-6">
                                      <label for="phone_number" class="form-label">No Hp</label>
                                      <input type="text" name="phone_number" id="phone_number"
                                          value="{{ $user->addresses->phone_number }}" class="form-control" required>
                                  </div>
                              </div>
                          </div>
                          <div class="card-footer text-end">
                              <button class="btn btn-primary" type="submit">Update Identitas</button>
                          </div>
                      </form>
                  </div>
              </div>

              {{-- Card 2: Password --}}
              <div class="col-12 mb-4">
                  <div class="card shadow-sm border-0">
                      <form id="updatePassword">
                          <div class="card-header bg-warning text-dark">
                              <h5 class="mb-0">Password</h5>
                          </div>
                          <div class="card-body">
                              <div class="row g-4">
                                  <div class="col-md-6">
                                      <label for="password_lama" class="form-label">Password Lama</label>
                                      <input type="password" id="password_lama" name="password_lama" class="form-control"
                                          required>
                                      <small id="errorpassword_lama" style="color: red"></small>

                                      <label for="password" class="form-label">Password Baru</label>
                                      <input type="password" id="password" name="password" class="form-control" required>
                                      <small id="passwordHelp" style="color: red"></small>
                                      <small id="errorpassword" style="color: red"></small>

                                      <label for="password_confirmation" class="form-label mt-3">Konfirmasi Password</label>
                                      <input type="password" id="password_confirmation" name="password_confirmation"
                                          class="form-control" required>
                                      <small id="confirmHelp" style="color: red"></small>
                                      <small id="errorpassword_confirmation" style="color: red"></small>
                                  </div>
                                  <div class="col-md-6">
                                      <p class="mb-2 fw-semibold">Persyaratan Password</p>
                                      <p class="small text-muted">Untuk membuat password baru, Anda harus memenuhi semua
                                          persyaratan berikut:</p>
                                      <ul class="small text-muted ps-3 mb-0">
                                          <li id="lengthCheck">Minimal 8 karakter</li>
                                          <li id="numberCheck">Minimal 1 angka</li>
                                      </ul>
                                  </div>
                              </div>
                          </div>
                          <div class="card-footer text-end">
                              <button class="btn btn-primary" type="submit">Update Password</button>
                          </div>
                      </form>
                  </div>
              </div>

              {{-- Card 3: Alamat --}}
              <div class="col-12 mb-4">
                  <div class="card shadow-sm border-0">
                      <form id="updateAlamat">
                          <div class="card-header bg-success text-white">
                              <h5 class="mb-0">Alamat</h5>
                          </div>
                          <div class="card-body">
                              <div class="row g-3">
                                  <div class="col-md-6">
                                      <label for="provinsi" class="form-label">Provinsi</label>
                                      <input type="text" id="provinsi" name="province" class="form-control"
                                          value="{{ $user->addresses->province }}" required>
                                  </div>
                                  <div class="col-md-6">
                                      <label for="kota" class="form-label">Kota</label>
                                      <input id="kota" class="form-control" name="city"
                                          value="{{ $user->addresses->city }}" required>
                                  </div>
                                  <div class="col-md-6">
                                      <label for="kecamatan" class="form-label">Kecamatan</label>
                                      <input id="kecamatan" class="form-control" name="district"
                                          value="{{ $user->addresses->district }}" required>
                                  </div>
                                  <div class="col-md-6">
                                      <label for="kelurahan" class="form-label">Kelurahan</label>
                                      <input id="kelurahan" class="form-control"
                                          value="{{ $user->addresses->subdistrict }}" name="subdistrict" required>
                                  </div>
                                  <div class="col-md-6">
                                      <label for="kode_pos" class="form-label">Kode Pos</label>
                                      <select id="kode_pos" class="form-select" name="postal_code" required></select>
                                  </div>
                                  <div class="col-12">
                                      <label for="alamat" class="form-label">Alamat Lengkap</label>
                                      <textarea id="alamat" name="full_address" class="form-control" rows="3" required>{{ $user->addresses->full_address }}</textarea>
                                  </div>
                              </div>
                          </div>
                          <div class="card-footer text-end">
                              <button class="btn btn-primary" type="submit">Update Alamat</button>
                          </div>
                      </form>
                  </div>
              </div>

          </div>
      </section>
  @endsection
  @push('scripts')
      <script>
          $(document).ready(function() {

              let provinsi = $('#provinsi').val();
              let kota = $('#kota').val();
              let kecamatan = $('#kecamatan').val();
              let kelurahan = $('#kelurahan').val();
              const search = `${provinsi} ${kota} ${kecamatan} ${kelurahan}`
              let kode_pos;

              loadSelectOptions('#kode_pos', `/wilayah/tujuan?search=${search}`,
                  '{{ $user->addresses->postal_code }}')
              // Fungsi validasi password
              function validatePassword() {
                  const password = $('#password').val();
                  const confirmation = $('#password_confirmation').val();

                  // Validasi persyaratan
                  const hasMinLength = password.length >= 8;
                  const hasNumber = /\d/.test(password);
                  const passwordsMatch = password === confirmation && password !== '';

                  // Update tampilan persyaratan
                  $('#lengthCheck').toggleClass('text-success', hasMinLength);
                  $('#numberCheck').toggleClass('text-success', hasNumber);
                  $('#matchCheck').toggleClass('text-success', passwordsMatch);

                  // Validasi konfirmasi password
                  if (confirmation.length > 0 && !passwordsMatch) {
                      $('#confirmHelp').text('Password tidak cocok');
                      $('#register-button').prop('disabled', true);
                  } else {
                      $('#confirmHelp').text('');
                      $('#register-button').prop('disabled', false);
                  }

                  return hasMinLength && hasNumber && passwordsMatch;
              }

              // Event listener untuk input password
              $('#password, #password_confirmation').on('input', function() {
                  validatePassword();
              });

              // Contoh validasi saat submit form
              $('#updatePassword').on('submit', function(e) {
                  e.preventDefault();
                  if (!validatePassword()) {
                      e.preventDefault();
                      $('#passwordHelp').text('Password tidak memenuhi semua persyaratan');
                  } else {
                      $('#passwordHelp').html('');
                  }

                  const url = '{{ route('profile.update.password') }}';
                  const data = new FormData(this);
                  const method = 'POST';
                  data.append('_method', 'PUT');

                  const successCallback = function(response) {
                      successToast(response.message);
                  }

                  const errorCallback = function(error) {
                      handleValidationErrors(error, '#updatePassword', [
                          'password_lama', 'password', 'password_confirmation'
                      ])
                  }

                  ajaxCall(url, method, data, successCallback, errorCallback);
              });

              $('#updateIdentitas').on('submit', function(e) {
                  e.preventDefault();

                  const url = '{{ route('profile.update') }}';
                  const data = new FormData(this);
                  const method = 'POST';

                  data.append('_method', 'PUT');

                  const successCallback = function(response) {
                      successToast(response.message);
                  }

                  const errorCallback = function(error) {
                      handleValidationErrors(error, '#updateIdentitas', [
                          'name', 'email', 'phone_number'
                      ])
                  }

                  ajaxCall(url, method, data, successCallback, errorCallback);
              });

              $('#updateAlamat').on('submit', function(e) {
                  e.preventDefault();

                  const url = '{{ route('profile.update.address') }}';
                  const data = new FormData(this);
                  const method = 'POST';

                  data.append('_method', 'PUT');

                  const successCallback = function(response) {
                      successToast(response.message);
                  }

                  const errorCallback = function(error) {
                      handleValidationErrors(error, '#updateIdentitas', [
                          'province', 'city', 'district', 'subdistrict', 'postal_code',
                          'full_address'
                      ])
                  }

                  ajaxCall(url, method, data, successCallback, errorCallback);
              });
          });
      </script>
  @endpush
