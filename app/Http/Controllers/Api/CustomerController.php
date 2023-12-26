<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use Illuminate\Http\Request;

class CustomerController extends Controller
{
    public function getCustomers(Request $request)
    {
        // Mengambil daftar pelanggan terbaru
        $customers = Customer::latest()->get();

        // Mengembalikan data pelanggan dalam format JSON
        return response()->json(['customers' => $customers], 200);
    }

    public function search(Request $request)
    {
        // Validasi request
        $request->validate([
            'search' => 'required|string|min:1',
        ]);

        // Mencari pelanggan berdasarkan keyword
        $query = $request->input('search');
        $results = Customer::where('name', 'like', "%$query%")
            ->orWhere('email', 'like', "%$query%")
            ->get();

        // Mengembalikan hasil pencarian dalam format JSON
        return response()->json(['results' => $results], 200);
    }
}
