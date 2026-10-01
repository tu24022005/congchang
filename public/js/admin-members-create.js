/*
 * JavaScript riêng cho trang quản trị tạo tài khoản khách hàng.
 * Phụ trách xem trước ảnh đại diện trước khi gửi biểu mẫu.
 */

document.addEventListener('DOMContentLoaded', function () {
    const input = document.getElementById('avatarInput');
    const img = document.getElementById('avatarPreviewImg');
    const icon = document.getElementById('avatarDefaultIcon');

    if (!input) {
        return;
    }

    input.addEventListener('change', function (event) {
        const file = event.target.files[0];

        if (!file) {
            if (img) {
                img.src = '';
                img.classList.add('d-none');
            }
            if (icon) {
                icon.classList.remove('d-none');
            }
            return;
        }

        const reader = new FileReader();
        reader.onload = function (loadEvent) {
            if (img) {
                img.src = loadEvent.target.result;
                img.classList.remove('d-none');
            }
            if (icon) {
                icon.classList.add('d-none');
            }
        };
        reader.readAsDataURL(file);
    });
});
