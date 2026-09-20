<?php

namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;

class ProductAdvisorController extends Controller
{
    public function recommend(Request $request)
    {
        $validated = $request->validate([
            'skin_type' => ['required', 'in:dry,oily,combination,sensitive,normal'],
            'need' => ['required', 'string', 'max:120'],
        ]);

        $terms = [
            'dry' => ['dưỡng ẩm', 'cấp ẩm', 'khô', 'hyaluronic', 'kem'],
            'oily' => ['kiềm dầu', 'dầu', 'mụn', 'làm sạch', 'gel'],
            'combination' => ['cân bằng', 'dưỡng ẩm', 'kiềm dầu', 'lỗ chân lông'],
            'sensitive' => ['dịu nhẹ', 'nhạy cảm', 'phục hồi', ' calming', 'không hương liệu'],
            'normal' => ['chăm sóc', 'dưỡng', 'sáng da', 'cấp ẩm'],
        ][$validated['skin_type']];

        $need = trim($validated['need']);
        $products = Product::with(['category', 'variations'])
            ->where('quantity', '>', 0)
            ->where(function ($query) use ($terms, $need): void {
                foreach (array_unique(array_merge($terms, [$need])) as $term) {
                    if ($term !== '') {
                        $query->orWhere('name', 'like', '%' . $term . '%')
                            ->orWhere('description', 'like', '%' . $term . '%');
                    }
                }
            })
            ->latest()
            ->limit(4)
            ->get();

        if ($products->isEmpty()) {
            $products = Product::with(['category', 'variations'])
                ->where('quantity', '>', 0)
                ->latest()
                ->limit(4)
                ->get();
        }

        return response()->json([
            'message' => $this->messageFor($validated['skin_type'], $products->count()),
            'products' => $products->map(fn (Product $product): array => [
                'name' => $product->name,
                'url' => route('products.show', ['product' => $product->slug]),
                'image' => $product->image ? asset('storage/' . $product->image) : null,
                'price' => number_format($product->effectivePrice(), 0, ',', '.') . ' đ',
                'flash_sale' => $product->isFlashSaleActive(),
            ])->values(),
        ]);
    }

    private function messageFor(string $skinType, int $count): string
    {
        $labels = [
            'dry' => 'da khô',
            'oily' => 'da dầu',
            'combination' => 'da hỗn hợp',
            'sensitive' => 'da nhạy cảm',
            'normal' => 'da thường',
        ];

        return 'Mình đã chọn ' . $count . ' sản phẩm phù hợp hơn cho ' . $labels[$skinType] . '. Bạn hãy xem thành phần và thử trên vùng da nhỏ trước khi dùng nhé.';
    }
}
