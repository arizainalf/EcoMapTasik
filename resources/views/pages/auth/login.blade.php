@extends('layouts.auth')
@section('title', 'Login')
@section('content')
    <section class="py-5">
        <div class="container-fluid">

            <div class="bg-secondary py-5 my-5 rounded-5"
                style="background: url('{{ asset('FoodMart') }}/images/bg-leaves-img-pattern.png') no-repeat;">
                <div class="container">
                    <div class="row d-flex justify-content-center">
                        <div class="col-md-4 p-5">
                            <form id="form-login">
                                <div class="mx-auto text-center">
                                    <a href="{{ route('home') }}">
                                        <img src="{{ asset('storage/' . getSetting()->logo) }}" width="150px"
                                            class="img-thumbnail rounded-4">
                                    </a>
                                    <h2 class="my-3">Login</h2>
                                </div>
                                <div class="mb-3">
                                    <label for="" class="form-label">Email</label>
                                    <input type="email" class="form-control form-control-lg" name="email" id="email"
                                        placeholder="abc@mail.com">
                                    <small style="color: red" id="erroremail"></small>
                                </div>
                                <div class="mb-3">
                                    <label for="password" class="form-label">Password</label>
                                    <input type="text" class="form-control form-control-lg" name="password"
                                        id="password" placeholder="Password">
                                    <small style="color: red" id="errorpassword"></small>
                                </div>
                                <div class="d-flex justify-content-between align-items-center mb-3">
                                    <div class="form-check">
                                        <input class="form-check-input" type="checkbox" id="remember_me">
                                        <label class="form-check-label" for="remember_me">
                                            Remember me
                                        </label>
                                    </div>
                                    <a href="{{ route('register') }}" class="btn btn-link">Daftar</a>
                                </div>


                                <div class="d-grid gap-2">
                                    <button type="submit" class="btn btn-dark btn-lg">Login</button>
                                </div>
                            </form>

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
            $('#form-login').submit(function(e) {
                e.preventDefault();
                const url = '{{ route('login.store') }}';
                const data = new FormData(this);
                const method = 'POST';

                const successCallback = function(response) {
                    handleSuccess(response, null, "/admin");
                }

                const errorCallback = function(error) {
                    handleValidationErrors(error, '#form-login', [
                        'email', 'password'
                    ])
                }

                ajaxCall(url, method, data, successCallback, errorCallback);
            });
        });
    </script>
@endpush
