/* Hành vi dùng chung cho layout quản trị. */

/* Cập nhật số tin nhắn chưa đọc trên nút chat quản trị. */
(function () {
                const badge = document.getElementById('admin-chat-dock-badge');
                const refreshChatBadge = () => fetch('/chat/users').then(response => response.json()).then(users => {
                    const total = users.reduce((sum, user) => sum + Number(user.unread_messages_count || 0), 0);
                    badge.textContent = total > 99 ? '99+' : total;
                    badge.classList.toggle('d-none', total === 0);
                }).catch(() => {});
                refreshChatBadge();
                setInterval(refreshChatBadge, 15000);
            })();
