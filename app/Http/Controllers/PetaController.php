<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;

class PetaController extends Controller
{
    public function index(){
        $lokasi = Location::all();
        return view('pages.user.peta.index',compact('lokasi'));
    }
}
