<?php
namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\User;
use App\Traits\JsonResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    use JsonResponder;
    public function login(Request $request)
    {
        if ($request->method() === 'GET') {
            return view('pages.auth.login');
        }

        $credentials = $request->validate([
            'email'    => ['required', 'email', 'max:32'],
            'password' => ['required', 'min:6'],
        ]);

        $user = User::where('email', $credentials['email'])->first();

        if (! $user) {
            return $this->errorResponse(null, 'Email tidak ditemukan.');
        } else if (! Hash::check($credentials['password'], $user->password)) {
            return $this->errorResponse(null, 'Password salah.');
        }

        Auth::login($user, $request->remember);

        return $this->successResponse(
            $user,
            'Login berhasil.'
        );
    }
    public function register(Request $request)
    {
        if ($request->method() === 'GET') {
            return view('pages.auth.register');
        }
        $request->validate([
            'email'        => ['required', 'email', 'max:48', 'unique:users,email'],
            'firstName'    => ['required', 'string', 'max:50'],
            'lastName'     => ['nullable', 'string', 'max:50'],
            'password'     => ['required', 'string', 'max:100'],
            'phone_number' => ['required', 'string', 'max:100'],
            'province'     => ['required', 'string', 'max:100'],
            'city'         => ['required', 'string', 'max:100'],
            'district'     => ['required', 'string', 'max:100'],
            'subdistrict'  => ['required', 'string', 'max:100'],
            'postal_code'  => ['required', 'string', 'max:100'],
            'full_address' => ['required', 'string'],
        ]);

        DB::beginTransaction();

        try {

            $user = User::create([
                'name'     => $request->firstName . ' ' . $request->lastName,
                'email'    => $request->email,
                'password' => Hash::make($request->password),
            ]);

            Cart::create([
                'user_id' => $user->id,
            ]);

            Address::create([
                'user_id'      => $user->id,
                'name'         => $request->firstName . ' ' . $request->lastName,
                'phone_number' => $request->phone_number,
                'province'     => $request->province,
                'city'         => $request->city,
                'district'     => $request->district,
                'subdistrict'  => $request->subdistrict,
                'postal_code'  => $request->postal_code,
                'full_address' => $request->full_address,
            ]);

            DB::commit();

            return $this->successResponse(
                $user,
                'Berhasil mendaftar',
                201
            );

        } catch (\Exception $e) {
            DB::rollBack();

            return $this->errorResponse(
                null,
                'Gagal mendaftar. Silakan coba lagi. ' . $e->getMessage(),
                500// HTTP Internal Server Error
            );
        }

    }

    public function logout()
    {
        Auth::logout();

        return $this->successResponse(null, 'Logout berhasil.');
    }
}
