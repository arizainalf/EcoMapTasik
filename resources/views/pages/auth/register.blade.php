  @extends('layouts.auth')
  @section('title', 'Login')
  @section('content')
      <section class="py-5">
          <div class="container-fluid">

              <div class="bg-secondary py-5 my-5 rounded-5"
                  style="background: url('{{ asset('FoodMart') }}/images/bg-leaves-img-pattern.png') no-repeat;">
                  <div class="container my-5">
                      <form class="m-3" id="register-form">
                          <div class="mx-auto text-center my-4">
                              <a href="{{ route('home') }}">
                                  <img src="{{ asset('storage/' . getSetting()->logo) }}" width="150px"
                                      class="img-thumbnail rounded-4">
                              </a>
                              <h2 class="my-3">Daftar akun</h2>
                          </div>
                          <div class="row">
                              <div class="col-md-6">
                                  <div class="form-group">
                                      <label for="email">Email</label>
                                      <input type="email" name="email" class="form-control" id="email" required>
                                  </div>
                                  <div class="row">
                                      <div class="form-group col-md-6">
                                          <label for="firstname">Nama Depan</label>
                                          <input type="text" id="firstname" name="firstName" class="form-control"
                                              required>
                                      </div>
                                      <div class="form-group col-md-6">
                                          <label for="lastname">Nama Belakang</label>
                                          <input type="text" id="lastname" name="lastName" class="form-control">
                                      </div>
                                  </div>
                                  <div class="form-group">
                                      <label for="phone_number">No Hp</label>
                                      <input type="text" name="phone_number" class="form-control" id="phone_number"
                                          required>
                                  </div>
                                  <hr class="my-4">
                                  <div class="row mb-4">
                                      <div class="col-md-6">
                                          <div class="form-group">
                                              <label for="password">Password Baru</label>
                                              <input name="password" type="password" class="form-control" id="password"
                                                  required>
                                              <small id="passwordHelp" class="form-text text-danger"></small>
                                          </div>
                                          <div class="form-group">
                                              <label for="password_confirmation">Konfirmasi Password</label>
                                              <input type="password" class="form-control" id="password_confirmation"
                                                  required>
                                              <small id="confirmHelp" class="form-text text-danger"></small>
                                          </div>
                                      </div>
                                      <div class="col-md-6">
                                          <p class="mb-2">Persyaratan Password</p>
                                          <p class="small text-muted mb-2"> Untuk membuat password baru, Anda harus memenuhi
                                              semua
                                              persyaratan
                                              berikut: </p>
                                          <ul class="small text-muted pl-4 mb-0">
                                              <li id="lengthCheck"> Minimal 8 karakter </li>
                                              <li id="numberCheck">Minimal 1 angka</li>
                                          </ul>
                                      </div>
                                  </div>
                              </div>
                              <div class="col-md-6">
                                  <div class="form-row">
                                      <div class="form-group col-md-12">
                                          <label for="provinsi">Provinsi</label>
                                          <select id="provinsi" class="form-control w-full" required></select>
                                      </div>
                                      <div class="form-group col-md-12">
                                          <label for="kota">Kota</label>
                                          <select id="kota" class="form-control w-full" required></select>
                                      </div>
                                      <div class="form-group col-md-12">
                                          <label for="kecamatan">Kecamatan</label>
                                          <select id="kecamatan" class="form-control w-full" required></select>
                                      </div>
                                      <div class="form-group col-md-12">
                                          <label for="kelurahan">Kelurahan</label>
                                          <select id="kelurahan" class="form-control w-full" required></select>
                                      </div>
                                      <div class="form-group col-md-12">
                                          <label for="kode_pos">Kode Pos</label>
                                          <select id="kode_pos" class="form-control w-full" required></select>
                                      </div>
                                      <div class="form-group col-md-12">
                                          <label for="alamat">Alamat Lengkap</label>
                                          <textarea type="text" id="alamat" name="full_address" class="form-control"></textarea>
                                      </div>
                                  </div>
                              </div>
                          </div>
                          <div class="row my-2">
                              <div class="col-md-2 my-2">
                                  <div class="d-grid gap-2">
                                      <a href="{{ route('login') }}" class="btn btn-lg btn-outline-secondary btn-block"
                                          disabled type="submit">Login</a>
                                  </div>
                              </div>
                              <div class="col-md-10 my-2">
                                  <div class="d-grid gap-2">
                                      <button class="btn btn-lg btn-primary btn-block" disabled id="register-button"
                                          type="submit">Daftar</button>
                                  </div>
                              </div>
                              <input type="hidden" id="modeSwitcher">
                          </div>
                          <p class="mt-5 mb-3 text-muted text-center">© {{ date('Y') }}</p>
                      </form>
                  </div>
              </div>

          </div>
      @endsection
      @push('scripts')
          <script>
              $(document).ready(function() {

                  let provinsi;
                  let kota;
                  let kecamatan;
                  let kelurahan;
                  let kode_pos;

                  console.log('halo')
                  loadSelectOptions('#provinsi', '/wilayah/provinsi');

                  $('#provinsi').on('change', function() {
                      const provinsiId = $(this).val()
                      provinsi = $('#provinsi option:selected').text()

                      if (provinsiId) {
                          loadSelectOptions('#kota', `/wilayah/kota/${provinsiId}`)
                          $('#kota').prop('disabled', false)
                      } else {
                          $('#kota').empty().prop('disabled', true)
                      }

                      // Reset child select
                      $('#kecamatan').empty().prop('disabled', true)
                      $('#kelurahan').empty().prop('disabled', true)
                  })

                  $('#kota').on('change', function() {
                      const kotaId = $(this).val()
                      kota = $('#kota option:selected').text()

                      if (kotaId) {
                          loadSelectOptions('#kecamatan', `/wilayah/kecamatan/${kotaId}`)
                          $('#kecamatan').prop('disabled', false)
                      } else {
                          $('#kecamatan').empty().prop('disabled', true)
                      }

                      $('#kelurahan').empty().prop('disabled', true)
                  })

                  $('#kecamatan').on('change', function() {
                      const kecamatanId = $(this).val()
                      kecamatan = $('#kecamatan option:selected').text()

                      if (kecamatanId) {
                          loadSelectOptions('#kelurahan', `/wilayah/kelurahan/${kecamatanId}`)
                          $('#kelurahan').prop('disabled', false)
                      } else {
                          $('#kelurahan').empty().prop('disabled', true)
                      }
                  })

                  $('#kelurahan').on('change', function() {
                      const kelurahanId = $(this).val()
                      kelurahan = $('#kelurahan option:selected').text()

                      const search = `${provinsi} ${kota} ${kecamatan} ${kelurahan}`
                      console.log(search);
                      loadSelectOptions('#kode_pos', `/wilayah/tujuan?search=${search}`)
                  })

                  $('#kode_pos').on('change', function() {
                      kode_pos = $(this).val()
                  })
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
                  $('#register-form').on('submit', function(e) {
                      e.preventDefault();
                      if (!validatePassword()) {
                          e.preventDefault();
                          $('#passwordHelp').text('Password tidak memenuhi semua persyaratan');
                      } else {
                          $('#passwordHelp').html('');
                      }

                      const url = '{{ route('register.store') }}';
                      const data = new FormData(this);
                      const method = 'POST';

                      data.append('province', provinsi);
                      data.append('city', kota);
                      data.append('district', kecamatan);
                      data.append('subdistrict', kelurahan);
                      data.append('postal_code', kode_pos);

                      const successCallback = function(response) {
                          handleSuccess(response, null, "{{ route('login') }}");
                      }

                      const errorCallback = function(error) {
                          handleValidationErrors(error, '#register-form', [
                              'firstname', 'lastname', 'email', 'password'
                          ])
                      }

                      ajaxCall(url, method, data, successCallback, errorCallback);
                  });
              });
          </script>
      @endpush
