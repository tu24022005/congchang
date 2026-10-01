<?php

if (! function_exists('asset_v')) {
    /**
     * Trả về đường dẫn asset kèm query string version dựa trên filemtime để cache trình duyệt hiệu quả.
     */
    function asset_v(?string $path): string
    {
        if (blank($path)) {
            return '';
        }

        $cleanPath = ltrim($path, '/');
        $fullPath = public_path($cleanPath);

        if (! file_exists($fullPath)) {
            return asset($cleanPath);
        }

        return asset($cleanPath) . '?v=' . filemtime($fullPath);
    }
}
