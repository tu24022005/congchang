/* admin/products/show.blade.php - h?nh vi m?n h?nh */
document.addEventListener('DOMContentLoaded', function () {
    const mainImage = document.getElementById('main-product-image');
    document.querySelectorAll('.overview-thumb').forEach(function (thumb) {
        thumb.addEventListener('click', function () {
            if (mainImage.tagName === 'IMG') mainImage.src = this.dataset.image;
            document.querySelectorAll('.overview-thumb').forEach(item => item.classList.remove('active'));
            this.classList.add('active');
        });
    });
});

