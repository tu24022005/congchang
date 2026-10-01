<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('home_banners', function (Blueprint $table) {
            $table->string('video_path')->nullable()->after('image_url');
            $table->string('video_url', 2048)->nullable()->after('video_path');
            $table->boolean('video_autoplay')->default(true)->after('video_url');
            $table->boolean('video_loop')->default(true)->after('video_autoplay');
            $table->boolean('video_muted')->default(true)->after('video_loop');
        });
    }

    public function down(): void
    {
        Schema::table('home_banners', function (Blueprint $table) {
            $table->dropColumn([
                'video_path',
                'video_url',
                'video_autoplay',
                'video_loop',
                'video_muted',
            ]);
        });
    }
};
