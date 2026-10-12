<?php

namespace App\Http\Middleware;

use App\Models\RolePermission;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = $request->user();
        if (!$user || !in_array($user->role, $roles, true)) {
            abort(403, 'Bạn không có quyền thực hiện chức năng này.');
        }

        // Admin là vai trò tối cao; các vai trò còn lại có thể được tinh chỉnh từ ma trận.
        if ($user->role !== 'admin') {
            $permission = $this->permissionForRoute($request->route()?->getName());
            if ($permission && !RolePermission::query()
                ->where('role', $user->role)
                ->where('permission', $permission)
                ->where('allowed', true)
                ->exists()) {
                abort(403, 'Vai trò của bạn chưa được cấp quyền cho chức năng này.');
            }
        }

        return $next($request);
    }

    private function permissionForRoute(?string $routeName): ?string
    {
        if (!$routeName || !str_starts_with($routeName, 'admin.')) {
            return null;
        }

        return match (true) {
            $routeName === 'admin.dashboard' || $routeName === 'admin.chart.data' => 'dashboard',
            str_starts_with($routeName, 'admin.reports.') => 'reports',
            in_array($routeName, ['admin.products.index', 'admin.products.show'], true) => 'products.view',
            in_array($routeName, ['admin.products.create', 'admin.products.store', 'admin.products.destroy'], true) => 'products.create_delete',
            in_array($routeName, ['admin.products.edit', 'admin.products.update'], true) => 'products.edit',
            in_array($routeName, ['admin.products.stock', 'admin.products.variation-stock'], true) => 'inventory.edit',
            $routeName === 'admin.inventory-logs.index' => 'inventory.logs',
            in_array($routeName, ['admin.orders.index', 'admin.orders.updateStatus'], true) => 'orders.manage',
            in_array($routeName, ['admin.refunds.index', 'admin.orders.refund', 'admin.orders.refund.approve', 'admin.orders.refund.reject'], true) => 'refunds.manage',
            str_starts_with($routeName, 'admin.chat.') => 'chat',
            str_starts_with($routeName, 'admin.members.') => 'members.manage',
            str_starts_with($routeName, 'admin.vouchers.') || str_starts_with($routeName, 'admin.home-banners.') => 'marketing.manage',
            str_starts_with($routeName, 'admin.staff.') => 'staff.manage',
            default => null,
        };
    }
}
