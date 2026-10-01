/* orders/show.blade.php - h?nh vi m?n h?nh */
document.addEventListener('DOMContentLoaded', function () {
                        const bank = document.getElementById('refundBankSelect');
                        const bin = document.getElementById('refundBankBin');
                        if (bank && bin) {
                            bank.addEventListener('change', function () {
                                bin.value = this.options[this.selectedIndex].dataset.bin || '';
                            });
                        }
                    });

/* orders/show.blade.php - h?nh vi m?n h?nh */
// =========================================================================
// TRICK VÀNG: Đẩy toàn bộ Modal ra thẳng thẻ <body> để thoát khỏi lỗi xám màn hình
// =========================================================================
document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.modal').forEach(function(modal) {
        document.body.appendChild(modal);
    });
});

function copyOrderText(value) {
    navigator.clipboard.writeText(value).then(function () {
        const notice = document.createElement('div');
        notice.className = 'order-copy-notice';
        notice.innerHTML = '<i class="bi bi-check-circle me-2"></i>Đã sao chép thông tin';
        document.body.appendChild(notice);
        setTimeout(() => notice.remove(), 1800);
    });
}

document.querySelectorAll('.review-submit-button').forEach(function (button) {
    button.closest('form').addEventListener('submit', function (event) {
        const form = event.currentTarget;
        const rating = form.querySelector('input[name="rating"]').value;
        const message = form.querySelector('.rating-required-message');

        if (!rating) {
            event.preventDefault();
            message.classList.remove('d-none');
            form.querySelector('.rating-star-button').focus();
        } else {
            message.classList.add('d-none');
        }
    });
});

document.querySelectorAll('.star-rating-custom').forEach(function (ratingGroup) {
    const ratingInput = ratingGroup.querySelector('input[name="rating"]');
    const buttons = ratingGroup.querySelectorAll('.rating-star-button');

    buttons.forEach(function (button) {
        button.addEventListener('click', function () {
            const selectedRating = Number(button.dataset.rating);
            ratingInput.value = selectedRating;
            buttons.forEach(function (star) {
                const isSelected = Number(star.dataset.rating) <= selectedRating;
                star.classList.toggle('is-selected', isSelected);
                star.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
            });
            ratingGroup.closest('form').querySelector('.rating-required-message').classList.add('d-none');
        });
    });
});

function selectReviewRating(button) {
    const ratingGroup = button.closest('.star-rating-custom');
    const ratingInput = ratingGroup.querySelector('input[name="rating"]');
    const selectedRating = Number(button.dataset.rating);

    ratingInput.value = selectedRating;
    ratingGroup.querySelectorAll('.rating-star-button').forEach(function (star) {
        const isSelected = Number(star.dataset.rating) <= selectedRating;
        star.classList.toggle('is-selected', isSelected);
        star.setAttribute('aria-pressed', isSelected ? 'true' : 'false');
    });
    ratingGroup.closest('form').querySelector('.rating-required-message').classList.add('d-none');
}

