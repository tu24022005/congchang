<?php 
use Illuminate\Database\Migrations\Migration; 
use Illuminate\Database\Schema\Blueprint; 
use Illuminate\Support\Facades\Schema;

return new class extends Migration 
{ 
 /** 
 * Run the migrations. 
 */ 
 public function up(): void 
 { 
 Schema::table('users', function (Blueprint $table) { 
 // Thêm cột role, đặt giá trị mặc định là 'customer' (nằm sau cột password) 
 $table->string('role')->default('customer')->after('password'); 
 }); 
 } 
 /** 
 * Reverse the migrations. 
 */ 
 public function down(): void 
 { 
 Schema::table('users', function (Blueprint $table) { 
 // Xóa cột role nếu chạy lệnh rollback 
 $table->dropColumn('role'); 
 }); 
 } 
};

