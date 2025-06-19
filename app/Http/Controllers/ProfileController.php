<?php
namespace App\Http\Controllers;

use App\Models\Address;
use App\Traits\JsonResponder;
use Illuminate\Http\Request;

class ProfileController extends Controller
{
    use JsonResponder;
    public function index()
    {
        return view('pages.user.profile.index');
    }

    public function store(Request $request)
    {
        return view('pages.user.profile.index');
    }

    public function updateAddress(Request $request, string $user_id)
    {
        $validated = $request->validate([
            'provinsi'  => 'required',
            'kota'      => 'required',
            'kecamatan' => 'required',
            'kelurahan' => 'required',
            'kode_pos'  => 'required',
        ]);

        $alamat = Address::where('user_id', $user_id)->first();

        $validated['user_id'] = $user_id;

        if ($alamat) {
            $alamat->update($validated);
        } else {
            Address::create($validated);
        }

        return $this->successResponse(null, 'Berhasil memperbarui alamat.');

    }
}
