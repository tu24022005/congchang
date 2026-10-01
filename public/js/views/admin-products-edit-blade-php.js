/* admin/products/edit.blade.php - h?nh vi m?n h?nh */
document.addEventListener('DOMContentLoaded', function () {
    const imageInput = document.getElementById('image');
    const imagePreview = document.getElementById('main-image-preview');
    const galleryInput = document.getElementById('gallery');
    const galleryPreview = document.getElementById('gallery-preview');
    const description = document.getElementById('description');
    const descriptionCount = document.getElementById('description-count');

    imageInput?.addEventListener('change', function () {
        const file = this.files?.[0];
        if (file) imagePreview.src = URL.createObjectURL(file);
    });

    galleryInput?.addEventListener('change', function () {
        galleryPreview.innerHTML = '';
        Array.from(this.files || []).forEach(function (file) {
            const image = document.createElement('img');
            image.src = URL.createObjectURL(file);
            image.alt = file.name;
            galleryPreview.appendChild(image);
        });
    });

    const updateCount = () => descriptionCount.textContent = description.value.length;
    description.addEventListener('input', updateCount);
    updateCount();

    const list = document.getElementById('variations-list');
    let variationIndex = list.querySelectorAll('.variation-row').length;
    const reindexRows = () => list.querySelectorAll('.variation-row').forEach((row, index) => row.querySelectorAll('[name]').forEach(input => input.name = input.name.replace(/variations\[\d+\]/, `variations[${index}]`)));
    document.getElementById('add-variation').addEventListener('click', function () {
        const index = list.querySelectorAll('.variation-row').length;
        list.insertAdjacentHTML('beforeend', `<div class="row g-2 align-items-end variation-row mb-2"><div class="col-md-2"><label class="form-label small">Mã SKU</label><input name="variations[${index}][sku]" class="form-control editor-input" placeholder="SON-RED-01"></div><div class="col-md-2"><label class="form-label small">Màu</label><input name="variations[${index}][color]" class="form-control editor-input" placeholder="Đỏ"></div><div class="col-md-2"><label class="form-label small">Bộ nhớ / loại</label><input name="variations[${index}][storage]" class="form-control editor-input" placeholder="256GB"></div><div class="col-md-2"><label class="form-label small">Khối lượng</label><input type="number" step="0.01" min="0" name="variations[${index}][size_value]" class="form-control editor-input" placeholder="250"></div><div class="col-md-1"><label class="form-label small">Đơn vị</label><select name="variations[${index}][size_unit]" class="form-select editor-input"><option value="">-</option><option value="g">g</option><option value="kg">kg</option><option value="ml">ml</option><option value="l">l</option></select></div><div class="col-md-1"><label class="form-label small">Giá</label><input type="number" min="0" name="variations[${index}][price]" class="form-control editor-input"></div><div class="col-md-1"><label class="form-label small">Tồn</label><input type="number" min="0" name="variations[${index}][stock]" class="form-control editor-input" value="0"></div><div class="col-md-2"><label class="form-label small">Ảnh biến thể</label><input type="file" name="variations[${index}][image]" class="form-control form-control-sm" accept="image/*"><small class="text-muted">Để trống để giữ ảnh</small></div><div class="col-md-1 variation-actions"><button type="button" class="btn btn-outline-primary duplicate-variation" title="Nhân bản dòng"><i class="bi bi-copy"></i></button><button type="button" class="btn btn-outline-danger remove-variation" title="Xóa dòng"><i class="bi bi-trash"></i></button></div></div>`);
        variationIndex++;
    });
    list.addEventListener('click', event => {
        const row = event.target.closest('.variation-row');
        if (!row) return;
        if (event.target.closest('.remove-variation')) row.remove();
        if (event.target.closest('.duplicate-variation')) {
            const clone = row.cloneNode(true);
            clone.querySelector('input[name$="[id]"]')?.remove();
            list.appendChild(clone);
        }
        reindexRows();
        variationIndex = list.querySelectorAll('.variation-row').length;
    });
});

