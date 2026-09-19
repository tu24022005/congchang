<?php

namespace Database\Seeders;

use App\Models\Post;
use App\Models\PostCategory;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class BlogSeeder extends Seeder
{
    public function run(): void
    {
        $categories = collect([
            ['name' => 'Chăm sóc da', 'description' => 'Kiến thức xây dựng quy trình skincare phù hợp.'],
            ['name' => 'Trang điểm', 'description' => 'Mẹo trang điểm tự nhiên, lâu trôi và dễ áp dụng.'],
            ['name' => 'Chăm sóc tóc', 'description' => 'Bí quyết chăm sóc tóc chắc khỏe, mềm mượt.'],
            ['name' => 'Lối sống đẹp', 'description' => 'Thói quen lành mạnh giúp bạn tự tin và rạng rỡ hơn.'],
        ])->mapWithKeys(function (array $category) {
            $category['slug'] = Str::slug($category['name']);
            return [$category['slug'] => PostCategory::updateOrCreate(
                ['slug' => $category['slug']],
                $category
            )];
        });

        $authorId = User::where('role', 'admin')->value('id');
        $posts = [
            [
                'category' => 'cham-soc-da',
                'title' => 'Skincare routine buổi sáng 5 bước cho làn da tươi tỉnh',
                'excerpt' => 'Một quy trình buổi sáng đơn giản giúp làm sạch, cấp ẩm và bảo vệ da trước tác động của môi trường.',
                'content' => '<h2>Vì sao routine buổi sáng nên tối giản?</h2><p>Buổi sáng, làn da cần được làm sạch nhẹ nhàng và bảo vệ thay vì sử dụng quá nhiều lớp dưỡng. Một routine đều đặn sẽ dễ duy trì hơn một quy trình dài nhưng thiếu nhất quán.</p><h3>5 bước gợi ý</h3><ol><li>Rửa mặt với sản phẩm dịu nhẹ.</li><li>Dùng toner hoặc lotion cấp ẩm nếu da cần.</li><li>Thoa serum theo nhu cầu như cấp ẩm hoặc chống oxy hóa.</li><li>Khóa ẩm bằng kem dưỡng mỏng nhẹ.</li><li>Thoa kem chống nắng đủ lượng và dặm lại khi cần.</li></ol><p>Hãy quan sát phản ứng của da trong 2 đến 4 tuần trước khi thêm sản phẩm mới.</p>',
                'meta_title' => 'Bí quyết đánh thức làn da khỏe đẹp sau khi thức dậy',
                'meta_description' => 'Khám phá các bước chăm da đầu ngày dễ thực hiện, giúp da mềm mịn và sẵn sàng trước ánh nắng, bụi bẩn.',
                'featured_image' => 'products/beauty-01.jpg',
            ],
            [
                'category' => 'cham-soc-da',
                'title' => 'Cách chọn kem chống nắng phù hợp với từng loại da',
                'excerpt' => 'Da dầu, da khô hay da nhạy cảm sẽ phù hợp với kết cấu kem chống nắng khác nhau.',
                'content' => '<h2>Đọc bảng thành phần và chọn kết cấu</h2><p>Da dầu thường hợp với dạng gel, essence hoặc finish ráo. Da khô có thể ưu tiên công thức có chất dưỡng ẩm. Với da nhạy cảm, hãy thử trước trên vùng da nhỏ và chọn sản phẩm không có hương liệu nếu bạn dễ kích ứng.</p><h3>Ba điều không nên bỏ qua</h3><ul><li>Chọn chỉ số SPF từ 30 và có khả năng bảo vệ phổ rộng.</li><li>Thoa đủ lượng, khoảng hai đốt ngón tay cho mặt và cổ.</li><li>Dặm lại sau mỗi vài giờ khi hoạt động ngoài trời hoặc đổ mồ hôi.</li></ul><p>Kem chống nắng cần được dùng mỗi ngày, kể cả khi trời nhiều mây.</p>',
                'meta_title' => 'Giải mã lựa chọn chống nắng cho làn da của bạn',
                'meta_description' => 'So sánh kết cấu và lưu ý thành phần để tìm sản phẩm bảo vệ da thoải mái, không bí bết hay khô căng.',
                'featured_image' => 'products/beauty-02.jpg',
            ],
            [
                'category' => 'cham-soc-da',
                'title' => 'Phân biệt da thiếu nước và da khô: Đừng dưỡng sai cách',
                'excerpt' => 'Da khô là một loại da, còn thiếu nước là tình trạng có thể xuất hiện ở bất kỳ loại da nào.',
                'content' => '<h2>Da khô khác da thiếu nước như thế nào?</h2><p>Da khô thường thiếu dầu tự nhiên, dễ bong tróc và có cảm giác căng. Da thiếu nước lại thiếu độ ẩm, có thể tiết dầu nhiều hơn ở vùng chữ T nhưng vẫn căng sau khi rửa mặt.</p><h3>Routine hỗ trợ</h3><p>Hãy ưu tiên sữa rửa mặt dịu nhẹ, lớp dưỡng ẩm có humectant như glycerin hoặc hyaluronic acid, sau đó khóa ẩm bằng kem dưỡng phù hợp. Đừng quên giảm tần suất tẩy tế bào chết nếu da đang nhạy cảm.</p>',
                'meta_title' => 'Da căng rát hay bóng dầu: Tìm đúng nguyên nhân',
                'meta_description' => 'Nhận diện dấu hiệu hàng rào ẩm mất cân bằng và điều chỉnh các bước dưỡng da để gương mặt dễ chịu hơn.',
                'featured_image' => 'products/beauty-03.jpg',
            ],
            [
                'category' => 'trang-diem',
                'title' => 'Mẹo trang điểm tự nhiên cho người mới bắt đầu',
                'excerpt' => 'Bắt đầu từ lớp nền mỏng, màu sắc hài hòa và những sản phẩm dễ tán để có diện mạo trong trẻo.',
                'content' => '<h2>Trang điểm đẹp bắt đầu từ nền da</h2><p>Trước khi makeup, hãy dưỡng ẩm vừa đủ và chờ sản phẩm thấm. Lớp nền mỏng sẽ tự nhiên hơn khi da được chuẩn bị tốt.</p><ol><li>Dùng kem chống nắng và kem lót nếu cần.</li><li>Chấm kem nền ở vùng trung tâm rồi tán mỏng ra ngoài.</li><li>Dùng che khuyết điểm vừa đủ ở quầng thâm hoặc nốt mụn.</li><li>Thêm má hồng màu đào hoặc hồng đất để gương mặt có sức sống.</li><li>Chải mày, bấm mi và dùng son làm điểm nhấn.</li></ol><p>Ánh sáng tự nhiên là cách tốt nhất để kiểm tra lớp nền trước khi ra ngoài.</p>',
                'meta_title' => 'Công thức makeup trong veo cho diện mạo hàng ngày',
                'meta_description' => 'Gợi ý cách tạo vẻ ngoài tươi tắn với lớp nền nhẹ, màu má hài hòa và điểm nhấn vừa đủ cho người mới.',
                'featured_image' => 'products/beauty-04.jpg',
            ],
            [
                'category' => 'trang-diem',
                'title' => 'Cách giữ lớp nền lâu trôi trong ngày nóng',
                'excerpt' => 'Chuẩn bị da, chọn kết cấu mỏng và cố định đúng cách sẽ giúp lớp nền bền đẹp hơn.',
                'content' => '<h2>Chuẩn bị da trước khi makeup</h2><p>Đừng bỏ qua bước dưỡng ẩm, nhưng hãy chọn sản phẩm thấm nhanh và không quá dày. Sau kem chống nắng, nên đợi vài phút rồi mới bắt đầu lớp nền.</p><h3>Trình tự giúp nền bền hơn</h3><p>Chọn nền có độ bám phù hợp, tán từng lớp mỏng, phủ phấn nhẹ ở vùng dễ đổ dầu và xịt cố định sau cùng. Khi cần dặm, hãy dùng giấy thấm dầu trước rồi mới thêm sản phẩm để tránh nền bị dày.</p>',
                'meta_title' => 'Bí kíp makeup bền màu khi thời tiết oi nóng',
                'meta_description' => 'Từ bước dưỡng da đến thao tác dặm lại, đây là những lưu ý giúp gương mặt tươi tắn lâu hơn trong ngày hè.',
                'featured_image' => 'products/beauty-05.jpg',
            ],
            [
                'category' => 'cham-soc-toc',
                'title' => 'Routine chăm tóc cơ bản cho mái tóc khô xơ',
                'excerpt' => 'Tóc khô xơ cần được làm sạch vừa đủ, bổ sung độ ẩm và hạn chế nhiệt trong quá trình tạo kiểu.',
                'content' => '<h2>Ba thói quen nên duy trì</h2><ul><li>Chọn dầu gội phù hợp da đầu, chỉ tập trung làm sạch ở chân tóc.</li><li>Thoa dầu xả từ thân đến ngọn, tránh bôi quá sát da đầu.</li><li>Dùng khăn mềm thấm nước, không chà xát mạnh khi tóc còn ướt.</li></ul><p>Mỗi tuần có thể dùng mặt nạ ủ tóc một lần và thoa một lượng nhỏ tinh dầu ở phần ngọn. Nếu thường xuyên dùng máy sấy hoặc máy tạo kiểu, hãy sử dụng sản phẩm bảo vệ tóc trước nhiệt.</p>',
                'meta_title' => 'Cứu mái tóc xơ rối bằng những bước chăm sóc tại nhà',
                'meta_description' => 'Tìm hiểu cách làm sạch, dưỡng ngọn và giảm tác động từ nhiệt để tóc khô trở nên mềm mại, dễ chải hơn.',
                'featured_image' => 'products/beauty-06.jpg',
            ],
            [
                'category' => 'loi-song-dep',
                'title' => '5 thói quen nhỏ giúp làn da trông rạng rỡ hơn',
                'excerpt' => 'Làn da khỏe không chỉ đến từ mỹ phẩm mà còn từ giấc ngủ, nước uống và cách bạn chăm sóc bản thân.',
                'content' => '<h2>Đẹp bền vững từ những điều đơn giản</h2><p>Hãy bắt đầu bằng việc ngủ đủ, uống nước theo nhu cầu cơ thể, ăn đa dạng thực phẩm và vận động nhẹ mỗi ngày. Khi căng thẳng kéo dài, da cũng có thể trở nên nhạy cảm hơn.</p><ol><li>Rửa mặt và tẩy trang phù hợp mỗi ngày.</li><li>Thoa kem chống nắng đều đặn.</li><li>Thay vỏ gối thường xuyên.</li><li>Không tự ý nặn mụn viêm.</li><li>Ghi lại phản ứng của da khi dùng sản phẩm mới.</li></ol>',
                'meta_title' => 'Nền tảng của vẻ đẹp bắt đầu từ sinh hoạt hàng ngày',
                'meta_description' => 'Những việc nhỏ trong giấc ngủ, ăn uống, vệ sinh và vận động có thể tạo khác biệt tích cực cho diện mạo.',
                'featured_image' => 'products/beauty-07.jpg',
            ],
            [
                'category' => 'loi-song-dep',
                'title' => 'Checklist vệ sinh mỹ phẩm để dùng an toàn hơn',
                'excerpt' => 'Vệ sinh cọ, đóng nắp sản phẩm và kiểm tra hạn dùng là những việc nhỏ nhưng rất quan trọng.',
                'content' => '<h2>Đừng để dụng cụ makeup thành nơi tích tụ vi khuẩn</h2><p>Cọ và mút trang điểm nên được làm sạch định kỳ, phơi khô hoàn toàn ở nơi thoáng trước khi cất. Không dùng chung son, mascara hoặc cọ mắt với người khác.</p><h3>Kiểm tra sản phẩm định kỳ</h3><p>Hãy ghi lại ngày mở nắp, bảo quản mỹ phẩm tránh nắng nóng và đóng nắp ngay sau khi sử dụng. Nếu sản phẩm đổi mùi, đổi màu, tách lớp bất thường hoặc gây kích ứng, nên ngừng dùng.</p>',
                'meta_title' => 'Bảo quản đồ làm đẹp đúng cách để dùng lâu và an tâm',
                'meta_description' => 'Các nguyên tắc làm sạch dụng cụ, đóng nắp và kiểm tra chất lượng giúp hạn chế nguy cơ kích ứng khi sử dụng mỹ phẩm.',
                'featured_image' => 'products/beauty-08.jpg',
            ],
        ];

        foreach ($posts as $index => $post) {
            $category = $categories[$post['category']];
            unset($post['category']);
            Post::updateOrCreate(
                ['slug' => Str::slug($post['title'])],
                array_merge($post, [
                    'post_category_id' => $category->id,
                    'user_id' => $authorId,
                    'status' => 'published',
                    'published_at' => Carbon::now()->subDays(8 - $index)->setTime(9 + ($index % 3), 0),
                ])
            );
        }
    }
}
