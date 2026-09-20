<?php

namespace App\Http\Controllers;

use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
    public function store(Request $request)
    {
        $validated = $this->validateAddress($request);
        $user = $request->user();

        DB::transaction(function () use ($user, $validated): void {
            $makeDefault = (bool) ($validated['is_default'] ?? false)
                || !$user->addresses()->exists();
            $address = $user->addresses()->create([
                ...$validated,
                'is_default' => $makeDefault,
            ]);

            if ($makeDefault) {
                $this->clearOtherDefaults($user->id, $address->id);
            }
        });

        return back()->with('success', 'Đã lưu địa chỉ giao hàng.');
    }

    public function update(Request $request, Address $address)
    {
        $this->ensureOwner($request, $address);
        $validated = $this->validateAddress($request);
        $user = $request->user();
        $makeDefault = (bool) ($validated['is_default'] ?? false);

        DB::transaction(function () use ($user, $address, $validated, $makeDefault): void {
            $address->update([
                ...$validated,
                'is_default' => $makeDefault,
            ]);

            if ($makeDefault) {
                $this->clearOtherDefaults($user->id, $address->id);
            } elseif (!$user->addresses()->where('is_default', true)->exists()) {
                $address->update(['is_default' => true]);
            }
        });

        return back()->with('success', 'Đã cập nhật địa chỉ giao hàng.');
    }

    public function destroy(Request $request, Address $address)
    {
        $this->ensureOwner($request, $address);
        $wasDefault = $address->is_default;
        $user = $request->user();
        $address->delete();

        if ($wasDefault) {
            $user->addresses()->latest('id')->first()?->update(['is_default' => true]);
        }

        return back()->with('success', 'Đã xóa địa chỉ giao hàng.');
    }

    public function setDefault(Request $request, Address $address)
    {
        $this->ensureOwner($request, $address);
        DB::transaction(function () use ($request, $address): void {
            $address->update(['is_default' => true]);
            $this->clearOtherDefaults($request->user()->id, $address->id);
        });

        return back()->with('success', 'Đã đặt địa chỉ mặc định.');
    }

    private function validateAddress(Request $request): array
    {
        return $request->validate([
            'label' => ['required', 'string', 'max:50'],
            'recipient_name' => ['required', 'string', 'min:2', 'max:120'],
            'phone' => ['required', 'regex:/^(0|\+84)(3|5|7|8|9)[0-9]{8}$/'],
            'address' => ['required', 'string', 'min:10', 'max:500'],
            'is_default' => ['sometimes', 'boolean'],
        ]);
    }

    private function ensureOwner(Request $request, Address $address): void
    {
        abort_unless($address->user_id === $request->user()->id, 403);
    }

    private function clearOtherDefaults(int $userId, int $addressId): void
    {
        Address::where('user_id', $userId)
            ->where('id', '!=', $addressId)
            ->update(['is_default' => false]);
    }
}
