/* Trang admin/staff: đưa modal ra body và xử lý ma trận quyền. */
document.addEventListener('DOMContentLoaded', function () {
    // Đảm bảo modal không bị che khuất bởi backdrop Bootstrap.
    document.querySelectorAll('.modal').forEach(function (modal) {
        document.body.appendChild(modal);
    });

    const matrix = document.querySelector('.matrix-table');
    if (!matrix) return;

    // Khởi tạo các ô quyền từ dữ liệu server đã lưu.
    const roles = Array.from(matrix.querySelectorAll('thead [data-role]')).map((header) => header.dataset.role);
    const permissions = JSON.parse(matrix.dataset.permissions || '{}');
    const status = document.querySelector('[data-permissions-status]');

    matrix.querySelectorAll('tbody tr[data-permission]').forEach((row) => {
            const permission = row.dataset.permission;
            roles.forEach((role, index) => {
                const cell = row.cells[index + 1];
                if (!cell) return;

                const allowed = Boolean(permissions[role]?.[permission]);
                const button = document.createElement('button');
                button.type = 'button';
                button.className = 'permission-toggle';
                button.dataset.role = role;
                button.dataset.permission = permission;
                button.setAttribute('aria-pressed', String(allowed));
                button.setAttribute('aria-label', `${allowed ? 'Tắt' : 'Bật'} quyền ${permission} cho ${role}`);
                button.innerHTML = allowed
                    ? '<i class="bi bi-check-circle-fill"></i>'
                    : '<i class="bi bi-dash-circle"></i>';
                cell.replaceChildren(button);
            });
    });

    // Đổi trạng thái quyền ngay trên giao diện, chưa gửi lên server cho tới khi bấm Lưu.
    matrix.addEventListener('click', function (event) {
            const button = event.target.closest('.permission-toggle');
            if (!button) return;

            const enabled = button.getAttribute('aria-pressed') === 'true';
            button.setAttribute('aria-pressed', String(!enabled));
            button.setAttribute('aria-label', `${enabled ? 'Bật' : 'Tắt'} quyền ${button.dataset.permission} cho ${button.dataset.role}`);
            button.innerHTML = enabled
                ? '<i class="bi bi-dash-circle"></i>'
                : '<i class="bi bi-check-circle-fill"></i>';
    });

    // Gửi toàn bộ ma trận lên server bằng route cập nhật quyền.
    document.querySelector('[data-save-permissions]')?.addEventListener('click', async function () {
            const payload = { permissions: {} };
            roles.forEach((role) => {
                payload.permissions[role] = {};
                matrix.querySelectorAll(`.permission-toggle[data-role="${role}"]`).forEach((button) => {
                    payload.permissions[role][button.dataset.permission] = button.getAttribute('aria-pressed') === 'true';
                });
            });

            this.disabled = true;
            try {
                const response = await fetch(matrix.dataset.updateUrl, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                    },
                    body: JSON.stringify(payload),
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Không thể lưu ma trận phân quyền.');

                status.textContent = result.message;
                status.classList.remove('d-none', 'text-danger');
                status.classList.add('text-success');
                setTimeout(() => status.classList.add('d-none'), 3000);
            } catch (error) {
                status.textContent = error.message;
                status.classList.remove('d-none', 'text-success');
                status.classList.add('text-danger');
            } finally {
                this.disabled = false;
            }
    });

});
