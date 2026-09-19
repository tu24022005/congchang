<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class MemberController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', '!=', 'admin')
            ->withCount(['orders', 'loyaltyPointTransactions'])
            ->withSum(['orders as completed_spend' => fn ($orders) => $orders->where('status', 'completed')], 'total')
            ->latest();

        if ($request->filled('search')) {
            $search = trim($request->input('search'));
            $query->where(function ($builder) use ($search) {
                $builder->where('name', 'like', '%' . $search . '%')
                    ->orWhere('email', 'like', '%' . $search . '%');
            });
        }

        $members = $query->paginate(15)->withQueryString();

        return view('admin.members.index', compact('members'));
    }

    public function show(User $user)
    {
        abort_if($user->role === 'admin', 404);

        $user->loadCount('orders');
        $user->completed_spend = $user->orders()->where('status', 'completed')->sum('total');
        $membershipTier = User::membershipTierFor($user->completed_spend);
        $transactions = $user->loyaltyPointTransactions()->latest()->paginate(15);

        return view('admin.members.show', compact('user', 'transactions', 'membershipTier'));
    }
}
