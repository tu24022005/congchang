<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\ProductVariation;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdditionalProductSeeder extends Seeder
{
    /**
     * Add a repeatable catalogue of products for local/demo environments.
     *
     * The SKU prefix and product names are intentionally stable so running
     * db:seed again updates this catalogue instead of creating duplicates.
     */
    public function run(): void
    {
        $productImages = array_map(
            static fn (int $number): string => 'products/catalog-'.str_pad((string) $number, 2, '0', STR_PAD_LEFT).'.jpg',
            range(1, 23)
        );
        $productImages = array_merge($productImages, [
            'products/beauty-01.jpg',
            'products/beauty-02.jpg',
            'products/beauty-03.jpg',
            'products/beauty-04.jpg',
            'products/beauty-05.jpg',
            'products/beauty-06.jpg',
            'products/beauty-07.jpg',
            'products/beauty-08.jpg',
            'products/beauty-09.jpg',
            'products/beauty-10.jpg',
            'products/beauty-11.jpg',
            'products/beauty-12.jpg',
            'products/beauty-13.jpg',
            'products/beauty-14.jpg',
            'products/beauty-15.jpg',
            'products/beauty-16.jpg',
            'products/beauty-17.jpg',
        ]);

        $categories = collect([
            'Chăm sóc da',
            'Trang điểm',
            'Chăm sóc tóc',
            'Chăm sóc cơ thể',
        ])->mapWithKeys(fn (string $name) => [
            $name => Category::firstOrCreate(['name' => $name])->id,
        ]);

        $products = [
            ['Sữa rửa mặt Amino dịu nhẹ', 'Chăm sóc da', 159000, 80, 'Làm sạch sâu nhưng vẫn giữ lại độ ẩm tự nhiên cho da.'],
            ['Toner cân bằng hoa cúc', 'Chăm sóc da', 189000, 70, 'Nước cân bằng dịu nhẹ giúp da sẵn sàng cho các bước dưỡng tiếp theo.'],
            ['Kem dưỡng phục hồi Ceramide', 'Chăm sóc da', 349000, 55, 'Công thức ceramide hỗ trợ hàng rào bảo vệ và làn da khô nhạy cảm.'],
            ['Mặt nạ ngủ Vitamin B5', 'Chăm sóc da', 269000, 45, 'Mặt nạ ngủ cấp ẩm và làm dịu da sau một ngày dài.'],
            ['Tinh chất Niacinamide 10%', 'Chăm sóc da', 299000, 65, 'Tinh chất hỗ trợ cải thiện vẻ ngoài của lỗ chân lông và sắc da.'],
            ['Kem mắt Caffeine sáng mịn', 'Chăm sóc da', 319000, 40, 'Chăm sóc vùng da quanh mắt với caffeine và peptide nhẹ nhàng.'],
            ['Xịt khoáng trà xanh', 'Chăm sóc da', 129000, 90, 'Xịt khoáng tươi mát, tiện dụng cho làn da trong ngày.'],
            ['Dầu tẩy trang hoa trà', 'Chăm sóc da', 279000, 60, 'Dầu tẩy trang nhũ hóa nhanh, làm sạch lớp trang điểm và kem chống nắng.'],
            ['Son dưỡng bơ hạt mỡ', 'Chăm sóc da', 99000, 100, 'Son dưỡng không màu giúp đôi môi mềm mại và dễ chịu.'],
            ['Miếng dán mụn tràm trà', 'Chăm sóc da', 89000, 120, 'Miếng dán mỏng giúp bảo vệ nốt mụn trong sinh hoạt hằng ngày.'],
            ['Chì kẻ mày lâu trôi Brown Taupe', 'Trang điểm', 149000, 75, 'Chì kẻ mày đầu mảnh, dễ tạo dáng tự nhiên và lâu trôi.'],
            ['Bút kẻ mắt nước Espresso', 'Trang điểm', 169000, 70, 'Đường kẻ sắc nét với đầu bút linh hoạt, khô nhanh.'],
            ['Phấn phủ kiềm dầu Soft Matte', 'Trang điểm', 289000, 50, 'Phấn phủ mịn nhẹ giúp lớp nền bền đẹp và hạn chế bóng dầu.'],
            ['Kem lót cấp ẩm Glow Base', 'Trang điểm', 279000, 45, 'Kem lót tạo bề mặt mượt mà và hiệu ứng căng bóng tự nhiên.'],
            ['Che khuyết điểm Cover Fit', 'Trang điểm', 239000, 60, 'Độ che phủ vừa phải, tiệp da và không gây nặng mặt.'],
            ['Son thỏi Velvet Rose', 'Trang điểm', 229000, 80, 'Son lì mịn môi với sắc hồng đất dễ dùng mỗi ngày.'],
            ['Bảng tạo khối Face Sculpt', 'Trang điểm', 359000, 35, 'Bảng màu tạo khối và bắt sáng hài hòa cho nhiều phong cách.'],
            ['Phấn mắt nhũ Champagne', 'Trang điểm', 179000, 65, 'Màu nhũ champagne bắt sáng nhẹ, phù hợp cả ngày và tối.'],
            ['Má hồng dạng kem Coral Mist', 'Trang điểm', 219000, 55, 'Má hồng dạng kem dễ tán, cho gò má ửng màu tự nhiên.'],
            ['Xịt khóa nền Long Wear', 'Trang điểm', 249000, 50, 'Lớp xịt mỏng giúp cố định lớp trang điểm lâu hơn.'],
            ['Dầu gội bưởi giảm gãy rụng', 'Chăm sóc tóc', 239000, 70, 'Dầu gội hương bưởi làm sạch nhẹ và chăm sóc mái tóc yếu.'],
            ['Dầu xả phục hồi Protein', 'Chăm sóc tóc', 249000, 65, 'Dầu xả protein giúp tóc mềm, dễ chải và bớt xơ rối.'],
            ['Xịt dưỡng tóc chống nhiệt', 'Chăm sóc tóc', 219000, 60, 'Lớp dưỡng nhẹ bảo vệ tóc trước tác động của nhiệt tạo kiểu.'],
            ['Dầu gội khô hương bạc hà', 'Chăm sóc tóc', 189000, 50, 'Làm mới chân tóc nhanh chóng giữa những lần gội.'],
            ['Sáp vuốt tóc tự nhiên Matte Clay', 'Chăm sóc tóc', 199000, 45, 'Độ giữ nếp vừa phải với hiệu ứng lì tự nhiên, dễ gội sạch.'],
            ['Lược chải tóc chống rối', 'Chăm sóc tóc', 119000, 90, 'Răng lược mềm giúp gỡ rối nhẹ nhàng và hạn chế kéo tóc.'],
            ['Khăn ủ tóc sợi tre', 'Chăm sóc tóc', 139000, 55, 'Khăn ủ thấm hút tốt, tiện dụng trong quy trình chăm sóc tóc.'],
            ['Mặt nạ tóc dầu dừa', 'Chăm sóc tóc', 229000, 48, 'Mặt nạ dưỡng chuyên sâu cho mái tóc khô và thiếu sức sống.'],
            ['Tinh chất mọc tóc biotin', 'Chăm sóc tóc', 329000, 40, 'Tinh chất chăm sóc da đầu với biotin và chiết xuất thực vật.'],
            ['Bộ dầu gội mini Weekend', 'Chăm sóc tóc', 159000, 75, 'Bộ dầu gội và dầu xả nhỏ gọn cho những chuyến đi ngắn.'],
            ['Sữa tắm yến mạch dịu da', 'Chăm sóc cơ thể', 169000, 85, 'Sữa tắm yến mạch làm sạch êm dịu và lưu lại cảm giác mềm mại.'],
            ['Muối tắm thư giãn Lavender', 'Chăm sóc cơ thể', 199000, 50, 'Muối tắm hương lavender cho những phút thư giãn tại nhà.'],
            ['Dầu dưỡng thể hoa hồng', 'Chăm sóc cơ thể', 259000, 45, 'Dầu dưỡng mỏng nhẹ giúp da cơ thể trông mịn và sáng khỏe.'],
            ['Kem tay hương vanilla', 'Chăm sóc cơ thể', 109000, 100, 'Kem tay thấm nhanh, bổ sung độ ẩm mà không nhờn dính.'],
            ['Xà phòng thiên nhiên Oải hương', 'Chăm sóc cơ thể', 79000, 110, 'Xà phòng thực vật tạo bọt nhẹ với hương oải hương thư giãn.'],
            ['Bàn chải tắm cán gỗ', 'Chăm sóc cơ thể', 129000, 70, 'Bàn chải cán gỗ hỗ trợ làm sạch và massage cơ thể nhẹ nhàng.'],
            ['Kem gót chân bơ hạt mỡ', 'Chăm sóc cơ thể', 139000, 65, 'Kem dưỡng chuyên biệt giúp vùng gót chân mềm mại hơn.'],
            ['Nến thơm Cotton Clean', 'Chăm sóc cơ thể', 279000, 35, 'Nến thơm hương cotton sạch, tạo không gian thư thái.'],
            ['Bộ chăm sóc tay Mini Spa', 'Chăm sóc cơ thể', 189000, 45, 'Bộ mini gồm kem tay và mặt nạ tay cho đôi tay được chăm sóc.'],
            ['Khăn mặt cotton hữu cơ', 'Chăm sóc cơ thể', 99000, 100, 'Khăn cotton mềm, thấm hút tốt và phù hợp dùng hằng ngày.'],
        ];

        DB::transaction(function () use ($products, $categories, $productImages): void {
            foreach ($products as $index => [$name, $category, $price, $stock, $description]) {
                $number = $index + 1;
                $productImage = $productImages[$index];
                $product = Product::updateOrCreate(
                    ['name' => $name],
                    [
                        'category_id' => $categories[$category],
                        'description' => $description,
                        'quantity' => $stock,
                        'price' => $price,
                        'image' => $productImage,
                    ]
                );

                $firstStock = intdiv($stock, 2);
                foreach ([
                    ['A', 'Mặc định', $firstStock],
                    ['B', 'Phiên bản nâng cao', $stock - $firstStock],
                ] as [$suffix, $color, $variationStock]) {
                    $variation = ProductVariation::updateOrCreate(
                        ['sku' => 'SAMPLE-'.str_pad((string) $number, 2, '0', STR_PAD_LEFT).'-'.$suffix],
                        [
                            'product_id' => $product->id,
                            'color' => $color,
                            'storage' => null,
                            'size_value' => $suffix === 'A' ? 30 : 50,
                            'size_unit' => 'ml',
                            'price' => $price + ($suffix === 'B' ? 20000 : 0),
                            'stock' => $variationStock,
                        ]
                    );
                    $variationImage = $productImages[($index + ($suffix === 'A' ? 1 : 2)) % count($productImages)];
                    $variation->update(['image' => $variationImage]);
                }
            }
        });
    }
}
