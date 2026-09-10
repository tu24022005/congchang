<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::table('product_reviews')
            ->whereNull('media_paths')
            ->where('has_media', true)
            ->update(['has_media' => false]);
    }

    public function down(): void
    {
        // The old flag did not point to an actual stored file, so it is not restored.
    }
};