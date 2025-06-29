<?php
namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\User;
use App\Traits\JsonResponder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    use JsonResponder;
    public function index()
    {
        $user = User::with('addresses')->where('id', auth()->user()->id)->first();
        return view('pages.user.profile.index', compact('user'));
    }

    public function store(Request $request)
    {
        return view('pages.user.profile.index');
    }

    public function update(Request $request)
    {
        $validated = $request->validate([
            'name'         => 'required|string',
            'email'        => 'required|email|unique:users,email,' . auth()->user()->id,
            'phone_number' => 'required|string',
        ]);

        try {
            $user = User::findOrFail(auth()->user()->id);

            $user->name  = $validated['name'];
            $user->email = $validated['email'];

            $user->addresses()->update([
                'phone_number' => $validated['phone_number'],
            ]);

            $user->save();

            return $this->successResponse(null, 'Berhasil memperbarui profil.');
        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                'Gagal memperbarui profil.'
            );
        }
    }

    public function updatePassword(Request $request)
    {
        $validated = $request->validate([
            'password_lama'         => 'required|string',
            'password'              => 'required|string|min:8',
            'password_confirmation' => 'required|string|same:password',
        ]);

        try {
            $user = User::findOrFail(auth()->user()->id);

            if (! Hash::check($validated['password_lama'], $user->password)) {
                return $this->errorResponse(null, 'Kata sandi lama salah.');
            }

            if ($validated['password'] === $validated['password_lama']) {
                return $this->errorResponse(null, 'Kata sandi baru tidak boleh sama dengan kata sandi lama.');
            }

            $user->password = Hash::make($validated['password']);
            $user->save();

            return $this->successResponse(null, 'Berhasil memperbarui kata sandi.');
        } catch (\Exception $e) {
            return $this->errorResponse(
                $e->getMessage(),
                'Gagal memperbarui kata sandi.'
            );
        }

    }

    public function updateAddress(Request $request)
    {
        $validated = $request->validate([
            'province'    => 'required',
            'city'        => 'required',
            'district'    => 'required',
            'subdistrict' => 'required',
            'postal_code' => 'required',
        ]);

        $alamat = Address::where('user_id', auth()->user()->id)->first();

        $validated['user_id'] = auth()->user()->id;

        if ($alamat) {
            $alamat->update($validated);
        } else {
            Address::create($validated);
        }

        return $this->successResponse(null, 'Berhasil memperbarui alamat.');

    }
}
