<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class CompareController extends Controller
{
    public function index(Request $request)
    {
        $ids = $request->query('ids', '');
        $idArray = array_filter(array_map('intval', explode(',', (string) $ids)));

        $products = collect();
        if (!empty($idArray)) {
            $products = Product::with(['category', 'brand', 'variations', 'reviews'])
                ->whereIn('id', array_slice($idArray, 0, 3))
                ->get();
        }

        return view('products.compare', compact('products'));
    }
}
