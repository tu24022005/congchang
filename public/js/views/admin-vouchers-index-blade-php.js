/* admin/vouchers/index.blade.php - h?nh vi m?n h?nh */
document.getElementById('voucher-applies-to')?.addEventListener('change', function () {
    document.getElementById('voucher-category-field').classList.toggle('d-none', this.value !== 'category');
    document.getElementById('voucher-product-field').classList.toggle('d-none', this.value !== 'product');
});

