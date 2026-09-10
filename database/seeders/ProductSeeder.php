<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Product;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Tạo danh mục mỹ phẩm và chăm sóc cá nhân
        $categories = [
            'Chăm sóc da',
            'Trang điểm',
            'Chăm sóc tóc',
            'Chăm sóc cơ thể'
        ];

        $categoryIds = [];
        foreach ($categories as $catName) {
            $category = Category::create([
                'name' => $catName,
            ]);
            $categoryIds[] = $category->id;
        }

        // 2. Tạo 20 sản phẩm mỹ phẩm mẫu
        $products = [
            // Cụm 1: Chăm sóc da
            ['name' => 'Serum cấp ẩm Hyaluronic Aloha', 'cat' => $categoryIds[0], 'price' => 289000, 'qty' => 50, 'desc' => 'Serum cấp ẩm dịu nhẹ giúp làn da căng mịn và tươi sáng.'],
            ['name' => 'Kem chống nắng Daily Shield SPF50+', 'cat' => $categoryIds[0], 'price' => 329000, 'qty' => 45, 'desc' => 'Kem chống nắng phổ rộng, mỏng nhẹ, không để lại vệt trắng trên da.'],
            ['name' => 'Sữa rửa mặt trà xanh dịu nhẹ', 'cat' => $categoryIds[0], 'price' => 189000, 'qty' => 60, 'desc' => 'Làm sạch bụi bẩn và dầu thừa mà không gây khô căng da.'],
            ['name' => 'Mặt nạ đất sét thanh lọc da', 'cat' => $categoryIds[0], 'price' => 249000, 'qty' => 30, 'desc' => 'Mặt nạ đất sét giúp làm sạch lỗ chân lông và cân bằng bã nhờn.'],
            ['name' => 'Nước tẩy trang Micellar dịu êm', 'cat' => $categoryIds[0], 'price' => 219000, 'qty' => 55, 'desc' => 'Tẩy sạch lớp trang điểm và bụi mịn với công thức không cồn.'],
            
            // Cụm 2: Trang điểm
            ['name' => 'Son tint dưỡng môi Berry Glow', 'cat' => $categoryIds[1], 'price' => 219000, 'qty' => 80, 'desc' => 'Son tint màu trong trẻo, lâu trôi và bổ sung độ ẩm cho môi.'],
            ['name' => 'Kem nền mỏng nhẹ Natural Skin', 'cat' => $categoryIds[1], 'price' => 389000, 'qty' => 35, 'desc' => 'Lớp nền mỏng nhẹ, tiệp da và giữ vẻ tự nhiên suốt ngày dài.'],
            ['name' => 'Phấn má hồng Peach Blush', 'cat' => $categoryIds[1], 'price' => 199000, 'qty' => 50, 'desc' => 'Phấn má màu đào tươi tắn, dễ tán và phù hợp trang điểm hằng ngày.'],
            ['name' => 'Bảng mắt Nude Everyday', 'cat' => $categoryIds[1], 'price' => 429000, 'qty' => 25, 'desc' => 'Bảng màu trung tính dễ phối cho phong cách trang điểm tự nhiên.'],
            ['name' => 'Mascara cong mi Volume Up', 'cat' => $categoryIds[1], 'price' => 239000, 'qty' => 45, 'desc' => 'Làm cong và làm dày mi, hạn chế lem trong nhiều giờ.'],

            // Cụm 3: Chăm sóc tóc
            ['name' => 'Dầu gội phục hồi tóc hư tổn', 'cat' => $categoryIds[2], 'price' => 279000, 'qty' => 80, 'desc' => 'Làm sạch dịu nhẹ và hỗ trợ phục hồi mái tóc khô xơ.'],
            ['name' => 'Dầu xả mềm mượt Silk Care', 'cat' => $categoryIds[2], 'price' => 259000, 'qty' => 60, 'desc' => 'Nuôi dưỡng tóc mềm mượt, dễ chải và giảm rối.'],
            ['name' => 'Tinh dầu dưỡng tóc Argan', 'cat' => $categoryIds[2], 'price' => 319000, 'qty' => 40, 'desc' => 'Tinh dầu Argan giúp tóc bóng khỏe và hạn chế chẻ ngọn.'],
            ['name' => 'Mặt nạ ủ tóc Keratin', 'cat' => $categoryIds[2], 'price' => 289000, 'qty' => 35, 'desc' => 'Mặt nạ ủ chuyên sâu cho mái tóc cần được chăm sóc và phục hồi.'],
            ['name' => 'Lược gỗ massage da đầu', 'cat' => $categoryIds[2], 'price' => 99000, 'qty' => 100, 'desc' => 'Lược gỗ thân thiện giúp massage da đầu thư giãn mỗi ngày.'],

            // Cụm 4: Chăm sóc cơ thể
            ['name' => 'Sữa tắm hoa anh đào Soft Bloom', 'cat' => $categoryIds[3], 'price' => 179000, 'qty' => 70, 'desc' => 'Sữa tắm làm sạch dịu nhẹ với hương hoa thư giãn và lưu hương mềm mại.'],
            ['name' => 'Kem dưỡng thể Vitamin E', 'cat' => $categoryIds[3], 'price' => 229000, 'qty' => 55, 'desc' => 'Dưỡng ẩm cho làn da cơ thể mềm mại và mịn màng.'],
            ['name' => 'Tẩy tế bào chết cà phê', 'cat' => $categoryIds[3], 'price' => 199000, 'qty' => 45, 'desc' => 'Hạt cà phê mịn giúp làm sạch da chết và mang lại cảm giác thư giãn.'],
            ['name' => 'Lăn khử mùi Cotton Fresh', 'cat' => $categoryIds[3], 'price' => 129000, 'qty' => 80, 'desc' => 'Khử mùi nhẹ nhàng, khô thoáng với hương cotton sạch sẽ.'],
            ['name' => 'Bộ chăm sóc cá nhân Travel Kit', 'cat' => $categoryIds[3], 'price' => 249000, 'qty' => 30, 'desc' => 'Bộ sản phẩm nhỏ gọn tiện lợi cho những chuyến đi và ngày bận rộn.'],
        ];

        $products = array_map(function (array $product, int $index): array {
            $product['image'] = 'products/beauty-' . str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) . '.jpg';
            return $product;
        }, $products, array_keys($products));

        foreach ($products as $p) {
            Product::create([
                'name' => $p['name'],
                'category_id' => $p['cat'],
                'description' => $p['desc'],
                'quantity' => $p['qty'],
                'price' => $p['price'],
                'image' => $p['image'],
            ]);
        }
    }
}