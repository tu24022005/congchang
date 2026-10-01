/* auth/login.blade.php - h?nh vi m?n h?nh */
document.addEventListener('DOMContentLoaded', () => {
        // --- 2. HIỆU ỨNG DÂY ĐÈN ---
        const lightsContainer = document.getElementById('fairy-lights');
        const bulbCount = 45; // Số lượng đèn trải dài toàn bộ chiều rộng
        
        for (let i = 0; i < bulbCount; i++) {
            const bulb = document.createElement('div');
            bulb.className = 'light-bulb';
            
            // Random delay cho animation nhấp nháy từ 0s đến 3s
            const delay = Math.random() * 3;
            bulb.style.animationDelay = `-${delay}s`;
            
            // Tính toán vị trí x và y để tạo độ võng của dây đèn (nằm gọn trên cao, không chạm vào chữ)
            const xPos = (i / (bulbCount - 1)) * 100; // từ 0% đến 100%
            const yPos = Math.sin(Math.PI * (i / (bulbCount - 1))) * 22; // Võng tối đa 22px
            const randomY = Math.random() * 4 - 2;
            
            bulb.style.left = `calc(${xPos}% - 3px)`; // Căn giữa chấm đèn
            bulb.style.top = `${yPos + randomY + 3}px`; // Nằm gọn trên cao sát mép trên
            
            lightsContainer.appendChild(bulb);
        }
    });

    // Code xử lý form loading hiện tại
    document.querySelector('form').addEventListener('submit', function() {
        const btn = this.querySelector('button[type="submit"]');
        if (btn && this.checkValidity()) {
            btn.classList.add('btn-loading');
        }
    });

