<?php

use App\Models\Address;
use App\Models\BankAccount;
use App\Models\Cart;
use App\Models\CartProduct;
use App\Models\Category;
use App\Models\Product;
use App\Models\Setting;
use Carbon\Carbon;

if (! function_exists('generateBase64Image')) {
    function generateBase64Image($imagePath)
    {
        if (file_exists($imagePath)) {
            $data        = file_get_contents($imagePath);
            $type        = pathinfo($imagePath, PATHINFO_EXTENSION);
            $base64Image = 'data:image/' . $type . ';base64,' . base64_encode($data);

            return $base64Image;
        } else {
            return '';
        }
    }
}

if (! function_exists('getSetting')) {
    function getSetting()
    {
        $setting = Setting::first();
        return $setting;
    }
}

if (! function_exists('getCategory')) {
    function getCategory()
    {
        $categories = Category::with('products')->get();
        return $categories;
    }
}

if (! function_exists('getBanks')) {
    function getBanks()
    {
        $banks = BankAccount::all();
        return $banks;
    }
}

if (! function_exists('getAddress')) {
    function getAddress()
    {
        $addresses = Address::where('user_id', auth()->user()->id)->first();
        return $addresses;
    }
}

if (! function_exists('getCartId')) {
    function getCartId()
    {
        if (! isLogin()) {
            return null;
        } else if (! isUser()) {
            return null;
        }
        $cart = Cart::where('user_id', auth()->user()->id)->first();
        return $cart->id;
    }
}

if (! function_exists('getCart')) {

    function getCart()
    {
        if (! isLogin()) {
            return null;
        } else if (! isUser()) {
            return null;
        }
        $cart = Cart::with('cartProducts')->where('user_id', auth()->user()->id)->first();
        return $cart;
    }
}

if (! function_exists('getCartCount')) {
    function getCartCount()
    {
        if (! isLogin()) {
            return null;
        } else if (! isUser()) {
            return null;
        }
        $cart = Cart::with('cartProducts')->where('user_id', auth()->user()->id)->first();
        return $cart->cartProducts->count();
    }
}

if (! function_exists('randomColorHex')) {
    function randomColorHex(): string
    {
        return sprintf('%06X', mt_rand(0, 0xFFFFFF));
    }
}

if (! function_exists('getUiAvatar')) {
    function getUiAvatar($nama): string
    {
        $kata = explode(' ', trim($nama));

        if (count($kata) >= 2) {
            $namaUntukUrl = $kata[0] . '+' . $kata[1];
        } else {
            $namaUntukUrl = $kata[0] ?? '';
        }

        $url = 'https://ui-avatars.com/api/?background=' . randomColorHex() . '&color=fff&name=' . $namaUntukUrl;
        return $url;
    }
}

if (! function_exists('getCartProduct')) {
    function getCartProduct()
    {
        $product = CartProduct::with('product')->where('cart_id', getCartId())->get();
        return $product;
    }
}

if (! function_exists('isAdmin')) {
    function isAdmin()
    {
        return isLogin() && auth()->user()->role == 'admin';
    }
}

if (! function_exists('isLogin')) {
    function isLogin()
    {
        return auth()->check();
    }
}

if (! function_exists('isUser')) {
    function isUser()
    {
        return isLogin() && auth()->user()->role === 'user';
    }
}

if (! function_exists('getProducts')) {
    function getProducts()
    {
        $products = Product::with('category')->latest()->get();
        return $products;
    }
}

if (! function_exists('formatTanggal')) {
    function formatTanggal($tanggal = null, $format = 'l, j F Y')
    {
        $parsedDate = Carbon::parse($tanggal)->locale('id')->settings(['formatFunction' => 'translatedFormat']);
        return $parsedDate->format($format);
    }
}

if (! function_exists('formatRupiah')) {
    function formatRupiah($uang = null)
    {
        $uang = (int) $uang;
        $uang = number_format($uang, 0, '', '.');
        return 'Rp ' . $uang;
    }
}

if (! function_exists('bulan')) {
    function bulan()
    {
        return [
            'Januari',
            'Februari',
            'Maret',
            'April',
            'Mei',
            'Juni',
            'Juli',
            'Agustus',
            'September',
            'Oktober',
            'November',
            'Desember',
        ];
    }
}
