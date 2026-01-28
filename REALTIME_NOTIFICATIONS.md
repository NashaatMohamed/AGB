# Real-Time Notifications with Laravel Reverb

## Overview

This application uses **Laravel Reverb** for WebSocket-based real-time notifications. Users receive instant notifications for:

- 🤝 Friend requests sent
- ✅ Friend requests accepted
- ❤️ Post likes
- 💬 New comments on posts

## Architecture

### Broadcasting Channels

#### Private Channels (Authentication Required)
- `user.{userId}` - User-specific notifications

#### Public Channels
- `posts.{postId}.likes` - Real-time like count updates
- `posts.{postId}.comments` - Real-time comment additions

### Events

All events implement `ShouldBroadcast` and automatically create database notifications:

| Event | Description | Channel | Notification Created |
|-------|-------------|---------|---------------------|
| `FriendRequestSent` | When a user sends a friend request | `user.{receiver_id}` | ✅ Yes |
| `FriendRequestAccepted` | When a friend request is accepted | `user.{sender_id}` | ✅ Yes |
| `PostLiked` | When a post receives a like | `posts.{post_id}.likes` + `user.{owner_id}` | ✅ Yes (if not own post) |
| `NewCommentAdded` | When a comment is added to a post | `posts.{post_id}.comments` + `user.{owner_id}` | ✅ Yes (if not own post) |

## Setup

### 1. Install Dependencies

```bash
composer require laravel/reverb
php artisan reverb:install
```

### 2. Configure Environment

The `.env` file should have these Reverb settings (auto-generated):

```env
BROADCAST_CONNECTION=reverb

REVERB_APP_ID=your-app-id
REVERB_APP_KEY=your-app-key
REVERB_APP_SECRET=your-app-secret
REVERB_HOST="localhost"
REVERB_PORT=8080
REVERB_SCHEME=http

VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
VITE_REVERB_HOST="${REVERB_HOST}"
VITE_REVERB_PORT="${REVERB_PORT}"
VITE_REVERB_SCHEME="${REVERB_SCHEME}"
```

### 3. Install Frontend Dependencies

**Note:** If npm is blocked by PowerShell execution policy, the dependencies are already listed in `package.json`. They will be installed when you run the build process.

```bash
npm install
```

Or manually add to `package.json`:
```json
{
  "devDependencies": {
    "laravel-echo": "^1.16.1",
    "pusher-js": "^8.4.0-rc2"
  }
}
```

### 4. Run Migrations

```bash
php artisan migrate
```

This creates the `notifications` table:
- `user_id` - Notification recipient
- `type` - Notification type (friend_request, post_liked, etc.)
- `message` - Human-readable message
- `data` - JSON data (post_id, sender info, etc.)
- `read` - Boolean read status
- `created_at`, `updated_at` - Timestamps

## Running the Application

### Development Mode (Recommended)

Use the custom Composer script that runs all services concurrently:

```bash
composer run dev
```

This starts:
1. Laravel development server (`php artisan serve`) on `http://localhost:8000`
2. Queue worker (`php artisan queue:listen`) for background jobs
3. Reverb WebSocket server (`php artisan reverb:start`) on `ws://localhost:8080`
4. Vite dev server (`npm run dev`) for hot module replacement

### Manual Mode

Start each service in separate terminals:

```bash
# Terminal 1 - Laravel Server
php artisan serve

# Terminal 2 - Queue Worker
php artisan queue:listen

# Terminal 3 - Reverb WebSocket Server
php artisan reverb:start

# Terminal 4 - Vite Dev Server
npm run dev
```

### Production Mode

```bash
# Build frontend assets
npm run build

# Start Reverb in production mode
php artisan reverb:start --host=0.0.0.0 --port=8080

# Use a process manager like Supervisor to keep Reverb running
```

## Testing Real-Time Notifications

### 1. Open Two Browser Windows

- **Window A**: Login as User 1
- **Window B**: Login as User 2

### 2. Test Friend Requests

In **Window A**:
1. Go to User 2's profile
2. Click "Send Friend Request"

In **Window B**:
- 🔔 **Real-time notification appears** in notification bell
- Red badge shows unread count
- Toast notification pops up

### 3. Test Post Likes

In **Window A**:
1. Create a post

In **Window B**:
1. Go to News Feed
2. Like User 1's post

In **Window A**:
- 🔔 **Instant notification** that your post was liked
- Like count updates in real-time

### 4. Test Comments

In **Window B**:
1. Comment on User 1's post

In **Window A**:
- 🔔 **Instant notification** of new comment
- Comment appears immediately on the post

## Frontend Integration

### Echo Configuration

Located in `resources/js/bootstrap.js`:

```javascript
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

window.Pusher = Pusher;
window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});
```

### Notification Bell Component

Alpine.js component in `resources/js/app.js`:

```javascript
Alpine.data('notificationBell', () => ({
    unreadCount: 0,
    notifications: [],
    isOpen: false,

    init() {
        this.fetchUnreadCount();
        this.setupListeners();
    },

    setupListeners() {
        const userId = document.querySelector('meta[name="user-id"]')?.content;
        
        window.Echo.private(`user.${userId}`)
            .listen('FriendRequestSent', (e) => {
                this.unreadCount++;
                this.notifications.unshift(e.notification);
                this.showToast(e.notification.message);
            })
            .listen('PostLiked', (e) => { /* ... */ })
            .listen('NewCommentAdded', (e) => { /* ... */ });
    }
}));
```

## API Endpoints

### Notifications

| Method | Endpoint | Description |
|--------|----------|-------------|
| GET | `/notifications` | List all notifications (paginated) |
| GET | `/notifications/unread-count` | Get unread count |
| POST | `/notifications/{id}/read` | Mark notification as read |
| POST | `/notifications/mark-all-read` | Mark all as read |
| DELETE | `/notifications/{id}` | Delete notification |

### Example Response

```json
{
  "success": true,
  "notifications": [
    {
      "id": 1,
      "user_id": 2,
      "type": "friend_request",
      "message": "John Doe sent you a friend request",
      "data": {
        "friendship_id": 5,
        "sender_id": 1,
        "sender_name": "John Doe"
      },
      "read": false,
      "created_at": "2026-01-27T19:30:00Z"
    }
  ]
}
```

## Troubleshooting

### WebSocket Connection Fails

**Problem:** Notifications not appearing in real-time

**Check:**
1. Is Reverb running? `php artisan reverb:start`
2. Check browser console for WebSocket errors
3. Verify `.env` has correct `VITE_REVERB_*` values
4. Ensure `BROADCAST_CONNECTION=reverb` in `.env`

**Solution:**
```bash
# Restart Reverb
php artisan reverb:restart

# Clear config cache
php artisan config:clear

# Rebuild frontend
npm run build
```

### Notifications Not Saving to Database

**Problem:** Events broadcast but no database records

**Check:**
1. Is the migrations run? `php artisan migrate`
2. Are events dispatched correctly in controllers?

**Solution:**
```php
// In controller
broadcast(new PostLiked($post, auth()->user()))->toOthers();
```

### Private Channel Authentication Fails

**Problem:** "Auth failed" errors in console

**Check:**
1. Is `routes/channels.php` loaded? (Check `bootstrap/app.php`)
2. Is user authenticated?
3. Is CSRF token present?

**Solution:**
```php
// bootstrap/app.php
->withRouting(
    channels: __DIR__.'/../routes/channels.php',
)
```

### npm/npx Blocked by PowerShell

**Problem:** PowerShell execution policy blocks npm

**Solution:**
```bash
# Option 1: Run Vite directly
node node_modules/vite/bin/vite.js

# Option 2: Change execution policy (Admin PowerShell)
Set-ExecutionPolicy -ExecutionPolicy RemoteSigned -Scope CurrentUser

# Option 3: Use Git Bash or WSL
```

## Performance Considerations

### Database Indexing

The notifications table has a composite index for fast queries:

```php
$table->index(['user_id', 'read', 'created_at']);
```

### Queue Jobs

Events are queued by default. Make sure queue worker is running:

```bash
php artisan queue:listen
```

### Scaling

For production with many concurrent users:

1. **Use Redis for queues:**
   ```env
   QUEUE_CONNECTION=redis
   ```

2. **Run multiple Reverb instances** behind a load balancer

3. **Use Laravel Horizon** for queue monitoring

4. **Enable database connection pooling**

## Security

### Private Channel Authorization

Private channels require authentication in `routes/channels.php`:

```php
Broadcast::channel('user.{userId}', function ($user, $userId) {
    return (int) $user->id === (int) $userId;
});
```

Only the notification owner can subscribe to their channel.

### CSRF Protection

All API requests include CSRF token:

```javascript
headers: {
    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
}
```

## Additional Features

### Toast Notifications

Simple fade-in/out toasts show when notifications arrive:

```javascript
showToast(message) {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 bg-blue-600 text-white px-6 py-3 rounded-lg shadow-lg';
    toast.textContent = message;
    document.body.appendChild(toast);
    
    setTimeout(() => toast.remove(), 3000);
}
```

### Notification Badge

Real-time unread count in navigation:

```blade
<span x-show="unreadCount > 0" x-text="unreadCount > 99 ? '99+' : unreadCount">
```

### Full Notifications Page

View all notifications at `/notifications` with:
- ✅ Mark as read buttons
- 🗑️ Delete buttons
- 📄 Pagination
- 🎨 Type-specific icons and colors
- 🔗 Quick actions (Accept/Decline friend requests, View posts)

## Next Steps

1. **Add Email Notifications:** Send emails for important notifications
2. **Push Notifications:** Use service workers for browser push
3. **Notification Preferences:** Let users control what they're notified about
4. **Read Receipts:** Track when notifications are viewed
5. **Notification Groups:** Group similar notifications together
