/* admin/products/create.blade.php - h?nh vi m?n h?nh */
document.addEventListener('DOMContentLoaded', function () {
    const list = document.getElementById('variations-list');
    let index = list.querySelectorAll('.variation-row').length;
    document.getElementById('add-variation').addEventListener('click', function () {
        list.insertAdjacentHTML('beforeend', `<div class="row g-2 align-items-end variation-row mb-2"><div class="col-md-2"><label class="form-label small">Mã SKU</label><input name="variations[${index}][sku]" class="form-control" placeholder="SON-RED-01"></div><div class="col-md-2"><label class="form-label small">Màu</label><input name="variations[${index}][color]" class="form-control" placeholder="Đỏ"></div><div class="col-md-2"><label class="form-label small">Bộ nhớ / loại</label><input name="variations[${index}][storage]" class="form-control" placeholder="256GB"></div><div class="col-md-2"><label class="form-label small">Khối lượng</label><input type="number" step="0.01" min="0" name="variations[${index}][size_value]" class="form-control" placeholder="250"></div><div class="col-md-1"><label class="form-label small">Đơn vị</label><select name="variations[${index}][size_unit]" class="form-select"><option value="">-</option><option value="g">g</option><option value="kg">kg</option><option value="ml">ml</option><option value="l">l</option></select></div><div class="col-md-1"><label class="form-label small">Giá bán</label><input type="number" min="0" name="variations[${index}][price]" class="form-control" required></div><div class="col-md-1"><label class="form-label small">Tồn</label><input type="number" min="0" name="variations[${index}][stock]" class="form-control" value="0" required></div><div class="col-md-2"><label class="form-label small">Ảnh biến thể</label><input type="file" name="variations[${index}][image]" class="form-control form-control-sm" accept="image/*"></div><div class="col-md-1 variation-actions"><button type="button" class="btn btn-outline-primary duplicate-variation" title="Nhân bản dòng"><i class="bi bi-copy"></i></button><button type="button" class="btn btn-outline-danger remove-variation" title="Xóa dòng"><i class="bi bi-trash"></i></button></div></div>`);
        index++;
    });
    list.addEventListener('click', event => {
        const row = event.target.closest('.variation-row');
        if (!row) return;
        if (event.target.closest('.remove-variation')) row.remove();
        if (event.target.closest('.duplicate-variation')) list.appendChild(row.cloneNode(true));
        list.querySelectorAll('.variation-row').forEach((currentRow, rowIndex) => currentRow.querySelectorAll('[name]').forEach(input => input.name = input.name.replace(/variations\[\d+\]/, `variations[${rowIndex}]`)));
        index = list.querySelectorAll('.variation-row').length;
    });
});

