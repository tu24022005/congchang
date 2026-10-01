/* orders/index.blade.php - h?nh vi m?n h?nh */
document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('cancelOnlineOrderModal');
    const form  = document.getElementById('cancelOnlineOrderForm');
    if (modal && form) {
        modal.addEventListener('show.bs.modal', function (e) {
            form.action = e.relatedTarget.dataset.orderUrl;
        });
        if (modal.parentElement !== document.body) document.body.appendChild(modal);
        const bank = document.getElementById('refundModalBankSelect');
        const bin  = document.getElementById('refundModalBankBin');
        bank.addEventListener('change', function () {
            bin.value = this.options[this.selectedIndex].dataset.bin || '';
        });
    }
});

