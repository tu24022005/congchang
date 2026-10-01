/* admin/posts/form.blade.php - h?nh vi m?n h?nh */
document.getElementById('featured_image')?.addEventListener('change', function (event) {
    const file = event.target.files[0], preview = document.getElementById('image-preview');
    if (!file) return;
    const reader = new FileReader();
    reader.onload = () => { preview.innerHTML = '<img src="' + reader.result + '" class="rounded-3" style="width:180px;height:110px;object-fit:cover" alt="Ảnh xem trước">'; };
    reader.readAsDataURL(file);
});

