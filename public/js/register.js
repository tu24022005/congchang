document.addEventListener('DOMContentLoaded', function () {
    const password = document.getElementById('password');
    const strength = document.getElementById('password-strength');

    if (!password || !strength) {
        return;
    }

    password.addEventListener('input', function () {
        const value = this.value;
        let score = 0;

        if (value.length >= 8) score++;
        if (/[A-Z]/.test(value)) score++;
        if (/[a-z]/.test(value)) score++;
        if (/[0-9]/.test(value)) score++;
        if (/[^A-Za-z0-9]/.test(value)) score++;

        strength.dataset.score = score;
        strength.querySelector('small').textContent = !value
            ? 'Nhập mật khẩu để kiểm tra độ mạnh'
            : score < 3
                ? 'Mật khẩu yếu'
                : score < 5
                    ? 'Mật khẩu khá'
                    : 'Mật khẩu mạnh';
    });
});
