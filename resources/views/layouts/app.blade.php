<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" id="htmlElement">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        @auth
        <meta name="user-id" content="{{ auth()->id() }}">
        @endauth

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
        <div class="min-h-screen">
            @include('layouts.navigation')

            <!-- Page Heading -->
            @isset($header)
                <header class="bg-white dark:bg-gray-800 shadow">
                    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                        {{ $header }}
                    </div>
                </header>
            @endisset

            <!-- Page Content -->
            <main>
                {{ $slot }}
            </main>
        </div>

        <!-- Global functions fallback -->
        <script>
            console.log('✓ Global functions script loaded');

            // Dark mode toggle
            window.toggleDarkMode = function() {
                const html = document.getElementById('htmlElement');
                const isDark = html.classList.contains('dark');

                if (isDark) {
                    // Switch to light mode
                    html.classList.remove('dark');
                    localStorage.setItem('darkMode', 'false');

                    // Update icons
                    document.getElementById('sunIcon').style.display = 'block';
                    document.getElementById('moonIcon').style.display = 'none';
                    document.getElementById('mobileToggleText').textContent = '🌙 Dark Mode';
                } else {
                    // Switch to dark mode
                    html.classList.add('dark');
                    localStorage.setItem('darkMode', 'true');

                    // Update icons
                    document.getElementById('sunIcon').style.display = 'none';
                    document.getElementById('moonIcon').style.display = 'block';
                    document.getElementById('mobileToggleText').textContent = '☀️ Light Mode';
                }
            };

            // Initialize dark mode on page load
            window.initializeDarkMode = function() {
                const html = document.getElementById('htmlElement');
                const isDarkMode = localStorage.getItem('darkMode') === 'true';

                if (isDarkMode) {
                    html.classList.add('dark');
                    document.getElementById('sunIcon').style.display = 'none';
                    document.getElementById('moonIcon').style.display = 'block';
                    document.getElementById('mobileToggleText').textContent = '☀️ Light Mode';
                } else {
                    html.classList.remove('dark');
                    document.getElementById('sunIcon').style.display = 'block';
                    document.getElementById('moonIcon').style.display = 'none';
                    document.getElementById('mobileToggleText').textContent = '🌙 Dark Mode';
                }
            };

            // Initialize dark mode
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', window.initializeDarkMode);
            } else {
                window.initializeDarkMode();
            }

            console.log('✓ Dark mode toggle registered');

            // Like toggle function
            window.toggleLike = async function(postId) {
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
                            // Update data attribute for persistence
                            likeButton.setAttribute('data-liked', liked ? 'true' : 'false');

                            // Update count text
                            const countSpan = likeButton.querySelector('.like-count');
                            if (countSpan) countSpan.textContent = `(${count})`;

                            // Update heart icon fill
                            const svg = likeButton.querySelector('svg');
                            if (svg) {
                                if (liked) {
                                    svg.classList.remove('fill-none');
                                    svg.classList.add('fill-current', 'text-red-600');
                                } else {
                                    svg.classList.add('fill-none');
                                    svg.classList.remove('fill-current', 'text-red-600');
                                }
                            }

                            // Update button colors
                            if (liked) {
                                likeButton.classList.remove('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-50', 'dark:hover:bg-gray-700');
                                likeButton.classList.add('text-red-600', 'hover:bg-red-50', 'dark:hover:bg-red-900/20');
                            } else {
                                likeButton.classList.remove('text-red-600', 'hover:bg-red-50', 'dark:hover:bg-red-900/20');
                                likeButton.classList.add('text-gray-600', 'dark:text-gray-300', 'hover:bg-gray-50', 'dark:hover:bg-gray-700');
                            }
                        }
                        console.log('Like updated:', { liked, count });
                    }
                } catch (error) {
                    console.error('Error toggling like:', error);
                }
            };
            console.log('✓ toggleLike function registered');

            // Profile dropdown toggle function
            window.toggleProfileDropdown = function(button) {
                console.log('Profile dropdown toggled');

                // Find the parent dropdown container
                const dropdownContainer = button.closest('.relative');
                if (!dropdownContainer) return;

                // Find the dropdown content div
                const dropdownContent = dropdownContainer.querySelector('[x-show]');
                if (!dropdownContent) return;

                // Toggle display
                const isHidden = dropdownContent.style.display === 'none' || !dropdownContent.style.display;

                if (isHidden) {
                    // Show dropdown
                    dropdownContent.style.display = 'block';
                    // Add click away listener
                    document.addEventListener('click', window.closeProfileDropdown);
                } else {
                    // Hide dropdown
                    dropdownContent.style.display = 'none';
                    // Remove click away listener
                    document.removeEventListener('click', window.closeProfileDropdown);
                }
            };

            window.closeProfileDropdown = function(event) {
                const dropdownContainer = event.target.closest('.relative');
                const button = event.target.closest('button');

                // If click is outside dropdown, close it
                if (!dropdownContainer) {
                    const allDropdowns = document.querySelectorAll('.relative [x-show]');
                    allDropdowns.forEach(dropdown => {
                        dropdown.style.display = 'none';
                    });
                    document.removeEventListener('click', window.closeProfileDropdown);
                }
            };
            console.log('✓ toggleProfileDropdown function registered');

            // Post menu toggle function
            window.togglePostMenu = function(button) {
                const container = button.closest('.relative');
                if (!container) return;

                // Get the menu ID from the container
                const containerId = container.id; // e.g., "postMenu-1"
                const postId = containerId.split('-')[1];
                const dropdown = document.getElementById(`postMenuDropdown-${postId}`);

                if (!dropdown) return;

                // Toggle display
                const isHidden = dropdown.style.display === 'none' || !dropdown.style.display;

                if (isHidden) {
                    // Close all other post menus first
                    document.querySelectorAll('[id^="postMenuDropdown-"]').forEach(menu => {
                        menu.style.display = 'none';
                    });

                    // Show this menu
                    dropdown.style.display = 'block';
                    document.addEventListener('click', window.closePostMenu);
                } else {
                    // Hide this menu
                    dropdown.style.display = 'none';
                    document.removeEventListener('click', window.closePostMenu);
                }
            };

            window.closePostMenu = function(event) {
                // If click is outside any post menu, close all
                if (!event.target.closest('.relative[id^="postMenu-"]')) {
                    document.querySelectorAll('[id^="postMenuDropdown-"]').forEach(menu => {
                        menu.style.display = 'none';
                    });
                    document.removeEventListener('click', window.closePostMenu);
                }
            };
            console.log('✓ togglePostMenu function registered');

            // Share function
            if (!window.sharePost) {
                window.sharePost = function(url, content) {
                    if (navigator.share) {
                        navigator.share({
                            title: 'Check out this post',
                            text: content.substring(0, 100) + '...',
                            url: url
                        }).catch(err => console.log('Error sharing:', err));
                    } else {
                        navigator.clipboard.writeText(url).then(() => {
                            alert('Post link copied to clipboard!');
                        }).catch(err => console.log('Error copying:', err));
                    }
                };
                console.log('✓ sharePost function registered');
            }

            // Notification dropdown functions
            let notificationState = {
                isOpen: false,
                unreadCount: 0,
                notifications: [],
                loading: false,
            };

            window.toggleNotificationDropdown = async function() {
                const panel = document.getElementById('notificationPanel');
                notificationState.isOpen = !notificationState.isOpen;

                if (notificationState.isOpen) {
                    panel.style.display = 'block';
                    await window.fetchNotifications();
                    // Add click away listener
                    document.addEventListener('click', window.closeNotificationDropdown);
                } else {
                    panel.style.display = 'none';
                    document.removeEventListener('click', window.closeNotificationDropdown);
                }
            };

            window.closeNotificationDropdown = function(event) {
                const dropdown = document.getElementById('notificationDropdown');
                if (!event.target.closest('#notificationDropdown')) {
                    const panel = document.getElementById('notificationPanel');
                    panel.style.display = 'none';
                    notificationState.isOpen = false;
                    document.removeEventListener('click', window.closeNotificationDropdown);
                }
            };

            window.fetchNotifications = async function() {
                if (notificationState.loading) return;

                notificationState.loading = true;
                try {
                    const response = await fetch('/notifications', {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await response.json();

                    if (data.success) {
                        notificationState.notifications = data.notifications || [];
                        notificationState.unreadCount = data.notifications?.filter(n => !n.read).length || 0;
                        window.renderNotifications();
                        window.updateNotificationBadge();
                    }
                } catch (error) {
                    console.error('Error fetching notifications:', error);
                } finally {
                    notificationState.loading = false;
                }
            };

            window.updateNotificationBadge = function() {
                const badge = document.getElementById('notificationBadge');
                const markAllBtn = document.getElementById('markAllBtn');
                const clearAllBtn = document.getElementById('clearAllBtn');

                if (notificationState.notifications.length > 0) {
                    if (clearAllBtn) clearAllBtn.style.display = 'block';

                    if (notificationState.unreadCount > 0) {
                        badge.style.display = 'inline-flex';
                        badge.textContent = notificationState.unreadCount > 99 ? '99+' : notificationState.unreadCount;
                        if (markAllBtn) markAllBtn.style.display = 'block';
                    } else {
                        badge.style.display = 'none';
                        if (markAllBtn) markAllBtn.style.display = 'none';
                    }
                } else {
                    badge.style.display = 'none';
                    if (markAllBtn) markAllBtn.style.display = 'none';
                    if (clearAllBtn) clearAllBtn.style.display = 'none';
                }
            };

            window.renderNotifications = function() {
                const list = document.getElementById('notificationList');

                if (notificationState.notifications.length === 0) {
                    list.innerHTML = '<div class="p-6 text-center text-gray-500 dark:text-gray-400 text-sm">No notifications yet</div>';
                    return;
                }

                list.innerHTML = notificationState.notifications.map(notification => `
                    <div onclick="window.handleNotificationClick(event, ${notification.id}, '${notification.data?.redirect_url || '#'}')"
                         class="px-4 py-3 hover:bg-gray-50 dark:hover:bg-gray-700 cursor-pointer border-b border-gray-100 dark:border-gray-700 last:border-0 transition-colors ${!notification.read ? 'bg-blue-50 dark:bg-blue-900/20' : ''}">
                        <p class="text-sm text-gray-900 dark:text-white">${notification.message}</p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">${new Date(notification.created_at).toLocaleString()}</p>
                    </div>
                `).join('');
            };

            window.handleNotificationClick = async function(event, notificationId, redirectUrl) {
                event.preventDefault();

                // Mark as read
                await window.markNotificationAsRead(notificationId);

                // Close dropdown
                document.getElementById('notificationPanel').style.display = 'none';
                notificationState.isOpen = false;

                // Navigate if URL exists
                if (redirectUrl && redirectUrl !== '#') {
                    window.location.href = redirectUrl;
                }
            };

            window.markNotificationAsRead = async function(notificationId) {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                    await fetch(`/notifications/${notificationId}/read`, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        }
                    });

                    // Update local state
                    const notification = notificationState.notifications.find(n => n.id === notificationId);
                    if (notification && !notification.read) {
                        notification.read = true;
                        notificationState.unreadCount--;
                        window.updateNotificationBadge();
                        window.renderNotifications();
                    }
                } catch (error) {
                    console.error('Error marking notification as read:', error);
                }
            };

            window.markAllNotificationsAsRead = async function() {
                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                    await fetch('/notifications/mark-all-read', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        }
                    });

                    // Update local state
                    notificationState.notifications.forEach(n => n.read = true);
                    notificationState.unreadCount = 0;
                    window.updateNotificationBadge();
                    window.renderNotifications();
                } catch (error) {
                    console.error('Error marking all as read:', error);
                }
            };

            window.clearAllNotifications = async function() {
                if (!confirm('Are you sure you want to delete all notifications?')) {
                    return;
                }

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
                    const response = await fetch('/notifications/clear-all', {
                        method: 'DELETE',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        }
                    });

                    const data = await response.json();
                    if (data.success) {
                        // Clear local state
                        notificationState.notifications = [];
                        notificationState.unreadCount = 0;
                        window.updateNotificationBadge();
                        window.renderNotifications();
                        window.showNotificationToast('All notifications cleared');
                    }
                } catch (error) {
                    console.error('Error clearing all notifications:', error);
                    alert('Error clearing notifications. Please try again.');
                }
            };

            // Initialize notifications on page load
            window.initializeNotifications = async function() {
                try {
                    const response = await fetch('/notifications/unread-count', {
                        headers: { 'Accept': 'application/json' }
                    });
                    const data = await response.json();
                    notificationState.unreadCount = data.count || 0;
                    window.updateNotificationBadge();
                } catch (error) {
                    console.error('Error initializing notifications:', error);
                }
            };

            // Real-time notification listener with Echo
            window.setupRealtimeNotifications = function() {
                const userId = document.querySelector('meta[name="user-id"]')?.content;
                if (!userId || !window.Echo) {
                    console.warn('Echo not available or user not authenticated');
                    return;
                }

                console.log('Setting up real-time listeners for user:', userId);

                // Listen for friend request sent
                window.Echo.private(`user.${userId}`)
                    .listen('FriendRequestSent', (e) => {
                        console.log('📬 Friend request received:', e);
                        if (e.notification) {
                            // Add to notifications
                            notificationState.notifications.unshift(e.notification);
                            notificationState.unreadCount++;

                            // Update UI
                            window.updateNotificationBadge();
                            if (notificationState.isOpen) {
                                window.renderNotifications();
                            }

                            // Show toast
                            window.showNotificationToast(e.notification.message);
                        }
                    })
                    .listen('FriendRequestAccepted', (e) => {
                        console.log('✅ Friend request accepted:', e);
                        if (e.notification) {
                            notificationState.notifications.unshift(e.notification);
                            notificationState.unreadCount++;
                            window.updateNotificationBadge();
                            if (notificationState.isOpen) {
                                window.renderNotifications();
                            }
                            window.showNotificationToast(e.notification.message);
                        }
                    })
                    .listen('PostLiked', (e) => {
                        console.log('❤️ Post liked:', e);
                        if (e.notification) {
                            notificationState.notifications.unshift(e.notification);
                            notificationState.unreadCount++;
                            window.updateNotificationBadge();
                            if (notificationState.isOpen) {
                                window.renderNotifications();
                            }
                            window.showNotificationToast(e.notification.message);
                        }
                    })
                    .listen('NewCommentAdded', (e) => {
                        console.log('💬 New comment:', e);
                        if (e.notification) {
                            notificationState.notifications.unshift(e.notification);
                            notificationState.unreadCount++;
                            window.updateNotificationBadge();
                            if (notificationState.isOpen) {
                                window.renderNotifications();
                            }
                            window.showNotificationToast(e.notification.message);
                        }
                    });

                console.log('✓ Real-time listeners setup complete');
            };

            // Toast notification function
            window.showNotificationToast = function(message) {
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-4 right-4 bg-blue-600 dark:bg-blue-700 text-white px-6 py-3 rounded-lg shadow-lg z-50 animate-pulse';
                toast.textContent = message;
                toast.style.animation = 'slideIn 0.3s ease-out';
                document.body.appendChild(toast);

                setTimeout(() => {
                    toast.style.animation = 'slideOut 0.3s ease-in';
                    setTimeout(() => toast.remove(), 300);
                }, 4000);
            };

            // Add CSS animations for toast
            const style = document.createElement('style');
            style.textContent = `
                @keyframes slideIn {
                    from { transform: translateX(400px); opacity: 0; }
                    to { transform: translateX(0); opacity: 1; }
                }
                @keyframes slideOut {
                    from { transform: translateX(0); opacity: 1; }
                    to { transform: translateX(400px); opacity: 0; }
                }
            `;
            document.head.appendChild(style);

            // Initialize on load
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function() {
                    window.initializeNotifications();
                    window.setupRealtimeNotifications();
                });
            } else {
                window.initializeNotifications();
                window.setupRealtimeNotifications();
            }

            console.log('✓ Notification functions registered');
        </script>
    </body>
</html>
