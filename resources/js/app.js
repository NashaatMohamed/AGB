import './bootstrap';

import Alpine from 'alpinejs';

console.log('✓ App.js loaded');

// Expose globally before Alpine starts

window.Alpine = Alpine;

console.log('✓ Alpine exposed to window');

// Global like toggle function (vanilla JS fallback)
window.toggleLike = async (postId) => {
    console.log('toggleLike called for post:', postId);
    try {
        const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
        const response = await fetch(`/posts/${postId}/likes`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
            },
        });

        const data = await response.json();
        console.log('Like response:', data);

        if (data.success) {
            const liked = data.data.liked;
            const count = data.data.likes_count;

            // Update the button
            const likeButton = document.querySelector(`#like-button-${postId}`);
            if (likeButton) {
                // Update count text
                const countSpan = likeButton.querySelector('.like-count');
                if (countSpan) countSpan.textContent = `(${count})`;

                // Update heart icon fill
                const svg = likeButton.querySelector('svg');
                if (svg) {
                    if (liked) {
                        svg.classList.remove('fill-none');
                        svg.classList.add('fill-current', 'text-red-600');
                        likeButton.classList.remove('text-gray-600', 'dark:text-gray-300');
                        likeButton.classList.add('text-red-600');
                    } else {
                        svg.classList.remove('fill-current', 'text-red-600');
                        svg.classList.add('fill-none');
                        likeButton.classList.remove('text-red-600');
                        likeButton.classList.add('text-gray-600', 'dark:text-gray-300');
                    }
                }
            }
            console.log('Like updated:', { liked, count });
        }
    } catch (error) {
        console.error('Error toggling like:', error);
    }
};

// Share function - defined in global scope
const sharePost = (url, content) => {
    if (navigator.share) {
        navigator.share({
            title: 'Check out this post',
            text: content.substring(0, 100) + '...',
            url: url
        }).catch(err => console.log('Error sharing:', err));
    } else {
        // Fallback: copy to clipboard
        navigator.clipboard.writeText(url).then(() => {
            alert('Post link copied to clipboard!');
        }).catch(err => console.log('Error copying:', err));
    }
};

// Expose to window
window.sharePost = sharePost;

// Like button component
Alpine.data('likeButton', (postId, initialLiked, initialCount) => ({
    liked: initialLiked,
    likesCount: initialCount,
    loading: false,

    async toggle() {
        console.log('Like button clicked for post:', postId);
        if (this.loading) {
            console.log('Already loading, skipping');
            return;
        }

        this.loading = true;
        console.log('Starting like toggle...');

        try {
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            console.log('CSRF Token found:', csrfToken ? 'Yes' : 'No');

            const url = `/posts/${postId}/likes`;
            console.log('Fetching URL:', url);

            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'application/json',
                },
            });

            console.log('Response status:', response.status);
            const data = await response.json();
            console.log('Response data:', data);

            if (data.success === true && data.data) {
                this.liked = data.data.liked;
                this.likesCount = data.data.likes_count;
                console.log('Like toggled successfully:', { liked: this.liked, count: this.likesCount });
            } else {
                console.error('Like failed - unexpected response format:', data);
            }
        } catch (error) {
            console.error('Error toggling like:', error);
        } finally {
            this.loading = false;
        }
    }
}));

// Comment form component
Alpine.data('commentForm', () => ({
    content: '',
    submitting: false,

    autoResize(event) {
        const textarea = event.target;
        textarea.style.height = 'auto';
        textarea.style.height = textarea.scrollHeight + 'px';
    },

    async submit() {
        if (this.submitting || !this.content.trim()) return;

        this.submitting = true;
        // Form will be submitted normally, not via AJAX
        // This just prevents double submission
    }
}));

// Notification bell component
Alpine.data('notificationBell', () => ({
    unreadCount: 0,
    notifications: [],
    isOpen: false,
    loading: false,

    init() {
        this.fetchUnreadCount();
        this.setupListeners();
    },

    async fetchUnreadCount() {
        try {
            const response = await fetch('/notifications/unread-count', {
                headers: {
                    'Accept': 'application/json',
                }
            });
            const data = await response.json();
            this.unreadCount = data.count;
        } catch (error) {
            console.error('Error fetching unread count:', error);
        }
    },

    async fetchNotifications() {
        if (this.loading) return;

        this.loading = true;
        try {
            const response = await fetch('/notifications', {
                headers: {
                    'Accept': 'application/json',
                }
            });
            const data = await response.json();
            this.notifications = data.notifications;
        } catch (error) {
            console.error('Error fetching notifications:', error);
        } finally {
            this.loading = false;
        }
    },

    async toggle() {
        this.isOpen = !this.isOpen;
        if (this.isOpen && this.notifications.length === 0) {
            await this.fetchNotifications();
        }
    },

    async markAsRead(notificationId) {
        try {
            await fetch(`/notifications/${notificationId}/read`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            });

            // Update local state
            const notification = this.notifications.find(n => n.id === notificationId);
            if (notification) {
                notification.read = true;
                this.unreadCount = Math.max(0, this.unreadCount - 1);
            }
        } catch (error) {
            console.error('Error marking notification as read:', error);
        }
    },

    async markAllAsRead() {
        try {
            await fetch('/notifications/mark-all-read', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                }
            });

            // Update local state
            this.notifications.forEach(n => n.read = true);
            this.unreadCount = 0;
        } catch (error) {
            console.error('Error marking all as read:', error);
        }
    },

    setupListeners() {
        // Get user ID from meta tag
        const userId = document.querySelector('meta[name="user-id"]')?.content;
        if (!userId || !window.Echo) return;

        // Listen to private user channel for notifications
        window.Echo.private(`user.${userId}`)
            .listen('FriendRequestSent', (e) => {
                this.unreadCount++;
                this.notifications.unshift(e.notification);
                this.showToast(e.notification.message);
            })
            .listen('FriendRequestAccepted', (e) => {
                this.unreadCount++;
                this.notifications.unshift(e.notification);
                this.showToast(e.notification.message);
            })
            .listen('PostLiked', (e) => {
                if (e.notification) {
                    this.unreadCount++;
                    this.notifications.unshift(e.notification);
                    this.showToast(e.notification.message);
                }
            })
            .listen('NewCommentAdded', (e) => {
                if (e.notification) {
                    this.unreadCount++;
                    this.notifications.unshift(e.notification);
                    this.showToast(e.notification.message);
                }
            });
    },

    showToast(message) {
        // Simple toast notification
        const toast = document.createElement('div');
        toast.className = 'fixed bottom-4 right-4 bg-blue-600 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-fade-in';
        toast.textContent = message;
        document.body.appendChild(toast);

        setTimeout(() => {
            toast.classList.add('animate-fade-out');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    }
}));

console.log('✓ All Alpine components registered');
Alpine.start();
console.log('✓ Alpine started');
