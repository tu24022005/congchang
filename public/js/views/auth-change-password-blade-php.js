/* auth/change-password.blade.php - h?nh vi m?n h?nh */
document.addEventListener('DOMContentLoaded', function () {
        const btnSendOtp = document.getElementById('btnSendOtp');
        const btnSendOtpText = document.getElementById('btnSendOtpText');
        const btnSendOtpIcon = document.getElementById('btnSendOtpIcon');
        const otpInput = document.getElementById('otp');
        const statusAlert = document.getElementById('otpStatusAlert');

        let countdownTimer = null;

        if (btnSendOtp) {
            btnSendOtp.addEventListener('click', function () {
                // Vô hiệu hóa nút và hiện trạng thái đang gửi
                btnSendOtp.disabled = true;
                btnSendOtpText.textContent = 'Đang gửi...';
                if (btnSendOtpIcon) {
                    btnSendOtpIcon.className = 'spinner-border spinner-border-sm me-1';
                }
                statusAlert.className = 'alert d-none mt-3 mb-0 rounded-3 py-2 px-3 small border-0';

                fetch('/change-password/send-otp', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({})
                })
                .then(response => response.json().then(data => ({ status: response.status, body: data })))
                .then(({ status, body }) => {
                    if (status === 200 && body.success) {
                        statusAlert.className = 'alert alert-success mt-3 mb-0 rounded-3 py-2 px-3 small border-0 bg-success-subtle text-success-emphasis';
                        statusAlert.innerHTML = '<i class="bi bi-check-circle-fill me-1"></i> ' + body.message;
                        otpInput.focus();

                        // Bắt đầu đếm ngược 60 giây
                        let secondsLeft = 60;
                        if (btnSendOtpIcon) {
                            btnSendOtpIcon.className = 'bi bi-clock-history me-1';
                        }
                        btnSendOtpText.textContent = `Gửi lại (${secondsLeft}s)`;

                        if (countdownTimer) clearInterval(countdownTimer);
                        countdownTimer = setInterval(function () {
                            secondsLeft--;
                            if (secondsLeft <= 0) {
                                clearInterval(countdownTimer);
                                btnSendOtp.disabled = false;
                                btnSendOtpText.textContent = 'Gửi lại mã OTP';
                                if (btnSendOtpIcon) {
                                    btnSendOtpIcon.className = 'bi bi-send-fill me-1';
                                }
                            } else {
                                btnSendOtpText.textContent = `Gửi lại (${secondsLeft}s)`;
                            }
                        }, 1000);
                    } else {
                        statusAlert.className = 'alert alert-danger mt-3 mb-0 rounded-3 py-2 px-3 small border-0 bg-danger-subtle text-danger-emphasis';
                        statusAlert.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> ' + (body.message || 'Không thể gửi mã OTP. Vui lòng thử lại sau.');
                        btnSendOtp.disabled = false;
                        btnSendOtpText.textContent = 'Gửi mã OTP';
                        if (btnSendOtpIcon) {
                            btnSendOtpIcon.className = 'bi bi-send-fill me-1';
                        }
                    }
                })
                .catch(err => {
                    statusAlert.className = 'alert alert-danger mt-3 mb-0 rounded-3 py-2 px-3 small border-0 bg-danger-subtle text-danger-emphasis';
                    statusAlert.innerHTML = '<i class="bi bi-exclamation-triangle-fill me-1"></i> Đã xảy ra lỗi kết nối. Vui lòng thử lại.';
                    btnSendOtp.disabled = false;
                    btnSendOtpText.textContent = 'Gửi mã OTP';
                    if (btnSendOtpIcon) {
                        btnSendOtpIcon.className = 'bi bi-send-fill me-1';
                    }
                });
            });
        }
    });
