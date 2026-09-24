<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\InventoryLog;
use App\Models\Product;
use App\Models\User;
use Illuminate\Http\Request;

class InventoryLogController extends Controller
{
    public function index(Request $request)
    {
        $request->validate([
            'date_from' => ['nullable', 'date'],
            'date_to' => ['nullable', 'date', 'after_or_equal:date_from'],
        ]);

        $logs = InventoryLog::with(['variation', 'product', 'user', 'reference'])
            ->where(function ($query) {
                $query->where('reason', '!=', 'Giữ hàng theo đơn hàng')
                    ->orWhereNull('reason');
            })
            ->when($request->filled('product_id'), fn ($query) => $query->where('product_id', $request->integer('product_id')))
            ->when($request->filled('type'), fn ($query) => $query->where('type', $request->input('type')))
            ->when($request->filled('search'), function ($query) use ($request) {
                $search = trim($request->input('search'));
                $query->where(function ($searchQuery) use ($search) {
                    $searchQuery->where('reason', 'like', '%' . $search . '%')
                        ->orWhereHas('product', fn ($productQuery) => $productQuery->where('name', 'like', '%' . $search . '%'))
                        ->orWhereHas('variation', fn ($variationQuery) => $variationQuery->where('sku', 'like', '%' . $search . '%'));
                });
            })
            ->when($request->filled('staff_id'), fn ($query) => $query->where('user_id', $request->integer('staff_id')))
            ->when($request->filled('date_from'), fn ($query) => $query->whereDate('created_at', '>=', $request->input('date_from')))
            ->when($request->filled('date_to'), fn ($query) => $query->whereDate('created_at', '<=', $request->input('date_to')))
            ->latest()
            ->paginate(25)
            ->withQueryString();

        $products = Product::orderBy('name')->get(['id', 'name']);
        $staff = User::whereIn('role', ['admin', 'manager', 'warehouse_staff'])
            ->orderBy('name')
            ->get(['id', 'name', 'role']);

        return view('admin.inventory-logs.index', compact('logs', 'products', 'staff'));
    }
}
