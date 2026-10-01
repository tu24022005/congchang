/* pages/contact.blade.php - h?nh vi m?n h?nh */
async function handleContactSubmit(event) {
    event.preventDefault();
    const btn = document.getElementById('btn-contact-submit');
    const name = document.getElementById('contact-name').value.trim();
    const phone = document.getElementById('contact-phone').value.trim();
    const email = document.getElementById('contact-email').value.trim();
    const topic = document.getElementById('contact-topic').value;
    const message = document.getElementById('contact-message').value.trim();
    
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>Đang gửi tin...';
    
    try {
        const res = await fetch('/contact', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || '',
                'Accept': 'application/json'
            },
            body: JSON.stringify({
                name,
                phone,
                email,
                subject: topic,
                message
            })
        });
        const data = await res.json();
        
        btn.disabled = false;
        if (res.ok) {
            btn.innerHTML = '<i class="bi bi-check2-circle me-2"></i>Đã gửi thành công!';
            btn.classList.replace('btn-primary', 'btn-success');
            
            if (typeof window.showToast === 'function') {
                window.showToast(data.message || `Cảm ơn bạn ${name}! BeatyCare đã nhận được thông tin và sẽ phản hồi sớm nhất.`, 'success');
            } else {
                alert(`Cảm ơn bạn ${name}! BeatyCare đã nhận được thông tin và sẽ phản hồi sớm nhất.`);
            }
            
            document.getElementById('contact-form').reset();
            setTimeout(() => {
                btn.classList.replace('btn-success', 'btn-primary');
                btn.innerHTML = '<i class="bi bi-paperplane-fill me-2"></i>Gửi thông tin liên hệ';
            }, 3000);
        } else {
            btn.innerHTML = '<i class="bi bi-paperplane-fill me-2"></i>Gửi thông tin liên hệ';
            if (typeof window.showToast === 'function') {
                window.showToast(data.message || 'Không thể gửi thông tin liên hệ, vui lòng thử lại.', 'warning');
            } else {
                alert(data.message || 'Không thể gửi thông tin liên hệ, vui lòng thử lại.');
            }
        }
    } catch(err) {
        console.error(err);
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-paperplane-fill me-2"></i>Gửi thông tin liên hệ';
        if (typeof window.showToast === 'function') {
            window.showToast('Lỗi kết nối máy chủ. Vui lòng thử lại sau.', 'danger');
        } else {
            alert('Lỗi kết nối máy chủ. Vui lòng thử lại sau.');
        }
    }
}
