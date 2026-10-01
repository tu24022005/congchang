/* admin/members/show.blade.php - h?nh vi m?n h?nh */
document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.modal').forEach(function (modal) {
            document.body.appendChild(modal);
        });

        const avatarInput = document.getElementById('customerAvatarInput');
        const avatarImg = document.getElementById('avatarPreviewImg');
        const initialsText = document.getElementById('avatarInitialsText');

        if (avatarInput) {
            avatarInput.addEventListener('change', function (e) {
                const file = e.target.files[0];
                if (file) {
                    const reader = new FileReader();
                    reader.onload = function (evt) {
                        if (avatarImg) {
                            avatarImg.src = evt.target.result;
                            avatarImg.classList.remove('d-none');
                        }
                        if (initialsText) {
                            initialsText.classList.add('d-none');
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });

