<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration
{
/**
* Run the migrations.

24

*/
public function up(): void
{
Schema::create('products', function (Blueprint $table) {
$table->id(); // Cột ID tự tăng
// Các cột thông tin sản phẩm
$table->string('name'); // Tên sản phẩm
$table->text('description')->nullable(); // Mô tả (cho phép để trống)
$table->integer('quantity'); // Số lượng
$table->decimal('price', 15, 2); // Giá tiền (lưu tối đa 15 chữ số, 2 số thập phân)
// Cột khóa ngoại liên kết với bảng categories
$table->foreignId('category_id')
->constrained('categories') // Tự động hiểu là liên kết với cột id của bảng categories
->onDelete('cascade'); // Khi xóa danh mục thì các sản phẩm thuộc danh mục đó cũng bị xóa theo
$table->timestamps(); // Tự động tạo 2 cột created_at và updated_at
});
}
/**
* Reverse the migrations.
*/
public function down(): void
{
Schema::dropIfExists('products');
}
};