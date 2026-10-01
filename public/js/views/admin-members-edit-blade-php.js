/* admin/members/edit.blade.php - h?nh vi m?n h?nh */
document.addEventListener('DOMContentLoaded', function () {
        const input = document.getElementById('avatarInput');
        const img = document.getElementById('avatarPreviewImg');
        const initials = document.getElementById('avatarInitialsText');

        if (input) {
            input.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (evt) {
                        if (img) {
                            img.src = evt.target.result;
                            img.classList.remove('d-none');
                        }
                        if (initials) {
                            initials.classList.add('d-none');
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });

