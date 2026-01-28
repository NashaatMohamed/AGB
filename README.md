# AGB - Social Media Platform 📱

A modern, real-time social media platform built with **Laravel 12**, **PHP 8.2+**, **Vite 7**, and **Tailwind CSS 4**.

---

## 📋 Table of Contents
1. [Features](#-features)
2. [Prerequisites](#-prerequisites)
3. [Installation Guide](#-installation-guide--step-by-step)
4. [Running the Project](#-running-the-project)
5. [Project Structure](#-project-structure)
6. [Available Commands](#-available-commands)
7. [Working with the Project](#-working-with-the-project)
8. [Troubleshooting](#-troubleshooting)

---

## 🚀 Features

### Core Social Features
- **Posts**: Create, read, update, and delete posts with rich interactions
- **Comments**: Add comments to posts with real-time notifications
- **Likes**: Like/unlike posts and comments with persistent state
- **Friend System**: Send/accept/reject friend requests with suggested friends
- **User Profiles**: View and edit user profiles with profile pictures

### Real-Time Capabilities
- **Instant Notifications**: Real-time notifications using Laravel Reverb and Echo
- **Live Updates**: Comment and like notifications delivered instantly
- **Event Broadcasting**: Friend requests, likes, and comments trigger real-time events

### Notification System
- **Notification Dropdown**: Quick access to all notifications with unread count badge
- **Mark All Read**: Quickly mark all notifications as read
- **Clear All**: Remove all notifications at once with confirmation
- **Notification Navigation**: Click notifications to navigate to the relevant content

### User Experience
- **Dark Mode**: Toggle between light and dark themes with localStorage persistence
- **Responsive Design**: Mobile-first design optimized for all devices
- **Post Menu**: Quick actions for post management (edit, delete) with authorization
- **Search & Suggestions**: Friend suggestions on home and friends pages (6-12 suggestions)
- **Web Share API**: Native share button for easy content sharing

---

## 🛠️ Prerequisites

Before you start, make sure you have the following installed on your computer:

### 1. **PHP 8.2 or Higher**
Check your PHP version:
```bash
php --version
```
If not installed, download from [php.net](https://www.php.net/downloads)

### 2. **Composer** (PHP Package Manager)
Check if installed:
```bash
composer --version
```
If not installed, download from [getcomposer.org](https://getcomposer.org/download/)

### 3. **Node.js 20.19+ or 22.12+**
Check your Node.js version:
```bash
node --version
npm --version
```
If not installed, download from [nodejs.org](https://nodejs.org/)

### 4. **Git** (for cloning the repository)
Check if installed:
```bash
git --version
```
If not installed, download from [git-scm.com](https://git-scm.com/)

### 5. **SQLite** (Usually comes with PHP)
This project uses SQLite as the default database - no separate installation needed!

---

## 📦 Installation Guide – Step by Step

### Step 1: Clone the Repository
Open your terminal/command prompt and run:
```bash
git clone https://github.com/NashaatMohamed/AGB.git
cd AGB
```

### Step 2: Install All Dependencies
This one command installs everything needed:
```bash
composer run setup
```

This command automatically:
- ✅ Installs all PHP packages (via Composer)
- ✅ Copies `.env.example` to `.env` (configuration file)
- ✅ Generates the application encryption key
- ✅ Creates and populates the SQLite database
- ✅ Installs all JavaScript packages (via npm)
- ✅ Builds frontend assets (CSS and JavaScript)

**This may take 2-3 minutes. Please be patient!**

### Step 3: Verify Installation
After setup completes, verify everything is working:
```bash
php artisan tinker
```
Then type `exit` to close the Tinker shell. If it opens without errors, you're good!

---

## 🚀 Running the Project

### Start the Development Server
Run this command to start the entire application:
```bash
composer run dev
```

This starts **4 services simultaneously**:
1. **Laravel Web Server** - Your application runs on `http://localhost:8000`
2. **Queue Listener** - Processes background jobs
3. **Laravel Reverb** - Real-time broadcasting server
4. **Vite HMR** - Hot module replacement for frontend changes

### Access the Application
Open your browser and go to: **http://localhost:8000**

You should see the AGB social media platform homepage.

### Register a New Account
1. Click "Register" on the homepage
2. Fill in your details (name, email, password)
3. Click "Register"
4. You're now logged in and can start using the platform!

### Create Test Data
To seed the database with sample posts, comments, and users:
```bash
php artisan db:seed
```

---

## 🏗️ Project Structure

Understanding the project layout:

```
AGB/
├── app/
│   ├── Models/              # Database models (User, Post, Comment, etc.)
│   ├── Http/
│   │   ├── Controllers/     # Handle requests and return responses
│   │   └── Requests/        # Validate user input
│   ├── Events/              # Real-time events for notifications
│   └── Services/            # Business logic for the application
│
├── resources/
│   ├── views/               # HTML templates (Blade)
│   ├── css/                 # Tailwind CSS styling
│   └── js/                  # JavaScript files
│
├── routes/
│   ├── web.php              # Web routes for the application
│   ├── api.php              # API endpoints (if building mobile app)
│   └── channels.php         # Real-time broadcasting channels
│
├── database/
│   ├── migrations/          # Database schema changes
│   ├── factories/           # Generate fake test data
│   └── seeders/             # Populate database with sample data
│
├── tests/                   # Automated tests (PHPUnit)
├── public/                  # Publicly accessible files
├── storage/                 # Logs, uploads, and caches
├── .env                     # Environment configuration (created after setup)
├── .env.example             # Template for .env file
└── composer.json            # PHP packages list
```

---

## 🎯 Available Commands

### Development Commands

**Start full development environment:**
```bash
composer run dev
```
Runs the web server, queue listener, Reverb, and Vite HMR.

**Build frontend assets (for production):**
```bash
npm run build
```

**Development build with file watching:**
```bash
npm run dev
```

**Format code with Pint:**
```bash
./vendor/bin/pint
```

**Run automated tests:**
```bash
composer run test
```

### Database Commands

**Migrate database (apply schema changes):**
```bash
php artisan migrate
```

**Seed database with sample data:**
```bash
php artisan db:seed
```

**Reset and reseed database:**
```bash
php artisan migrate:refresh --seed
```

### Cache Commands

**Clear all caches (if something seems broken):**
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

---

## 💻 Working with the Project

### Creating a Post
1. Click "Create Post" or go to home page
2. Write your content in the text area
3. Click "Post" button
4. Your post appears at the top of the feed

### Interacting with Posts
- **Like** a post by clicking the heart icon
- **Comment** by typing in the comment box
- **Delete** your own posts using the menu (3 dots)
- **Share** posts using the share button

### Managing Friends
1. Go to "Friends" page
2. Click "Add" on suggested users
3. They receive a friend request notification
4. Once accepted, they appear in your friends list

### Viewing Notifications
- Click the **bell icon** 🔔 in the top-right
- See all your notifications with an unread count
- Click a notification to navigate to the related content
- **Mark all read** or **Clear all** using the buttons

### Dark Mode
- Click the moon/sun icon in the top-right corner
- Your preference is saved and persists across sessions

---

## 🧪 Testing the Platform

### Run Tests
```bash
composer run test
```

All tests use an in-memory SQLite database, so they run quickly without affecting your data.

---

## 🐛 Troubleshooting

### Issue: "Command 'composer' not found"
**Solution:** Composer is not installed or not in your PATH.
- Install from [getcomposer.org](https://getcomposer.org/)
- Or use `php composer.phar` instead of `composer`

### Issue: "Port 8000 already in use"
**Solution:** Another application is using port 8000.
```bash
php artisan serve --port=8001
```
Then access `http://localhost:8001`

### Issue: Database file not created
**Solution:** Create the database file manually:
```bash
touch database/database.sqlite
php artisan migrate
```

### Issue: "No such file or directory: .env"
**Solution:** Run setup again:
```bash
composer run setup
```

### Issue: Assets not loading (CSS/JavaScript broken)
**Solution:** Rebuild the frontend:
```bash
npm install
npm run build
```

### Issue: Real-time notifications not working
**Solution:** Make sure Reverb is running (part of `composer run dev`):
- The Reverb server should show messages in your terminal
- Refresh the page after clearing browser cache

### Issue: Application seems slow or broken after code changes
**Solution:** Clear all caches:
```bash
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear
```

### Issue: Stuck processes (Ctrl+C doesn't work)
**Solution:** Kill processes on the port:
```bash
# On Windows
netstat -ano | findstr :8000
taskkill /PID <PID> /F

# On Mac/Linux
lsof -ti:8000 | xargs kill -9
```

---

## 📚 Quick Reference Table

| Task | Command |
|------|---------|
| **Install everything** | `composer run setup` |
| **Start development** | `composer run dev` |
| **Build frontend** | `npm run build` |
| **Run tests** | `composer run test` |
| **Format code** | `./vendor/bin/pint` |
| **Seed database** | `php artisan db:seed` |
| **Clear caches** | `php artisan config:clear && php artisan cache:clear` |

---

## 🔌 Architecture & Code Organization

### Service Layer
Business logic extracted into service classes to prevent code duplication:

| Service | Purpose |
|---------|---------|
| `PostService` | Creating, updating, deleting posts |
| `CommentService` | Managing comments |
| `LikeService` | Toggling likes |
| `FriendshipService` | Friend request operations |
| `NotificationService` | Notification handling |

### Form Request Validation
All user input is validated using dedicated FormRequest classes:
- `StorePostRequest` - Validates new posts
- `UpdatePostRequest` - Validates post edits
- `StoreCommentRequest` - Validates comments
- `SendFriendRequestRequest` - Validates friend requests

### Response Macros
Unified JSON responses across the application:
```php
response()->success($data)      // 200 OK
response()->error($message)     // 400 Error
response()->notFound()          // 404 Not Found
response()->unauthorized()      // 401 Unauthorized
```

---

## 🔔 Real-Time System (Laravel Reverb + Echo)

The application broadcasts 4 types of events in real-time:

1. **FriendRequestSent** - Notifies user when someone sends a friend request
2. **FriendRequestAccepted** - Notifies user when friend request is accepted
3. **PostLiked** - Notifies post author when someone likes their post
4. **NewCommentAdded** - Notifies post author when someone comments

These events automatically push notifications to the frontend via Echo listeners, displayed in the notification bell dropdown.

---

## 🧪 Testing

The project includes automated tests with PHPUnit:

```bash
# Run all tests
composer run test

# Run specific test file
php artisan test tests/Feature/PostTest.php

# Run test with output
php artisan test --verbose
```

Tests use:
- ✅ In-memory SQLite (fast, no side effects)
- ✅ Array cache (no redis needed)
- ✅ Synchronous queues (no background workers needed)

---

## 📝 Environment Setup

The `.env` file contains your configuration. Key variables:

```env
# Application
APP_NAME=AGB
APP_ENV=local              # Change to 'production' for deployment
APP_DEBUG=true             # Change to 'false' for production
APP_URL=http://localhost:8000

# Database (SQLite is already configured)
DB_CONNECTION=sqlite
DB_DATABASE=database/database.sqlite

# Mail (optional, for sending emails)
MAIL_DRIVER=log

# Broadcasting (for real-time features)
BROADCAST_DRIVER=reverb
REVERB_APP_ID=12345
REVERB_APP_KEY=12345
```

---

## 🔒 Security Features

✅ **CSRF Protection** - All forms protected with CSRF tokens  
✅ **Authorization** - Users can only edit/delete their own content  
✅ **Input Validation** - All user input validated server-side  
✅ **Password Hashing** - Passwords hashed with bcrypt  
✅ **SQL Injection Prevention** - Using Eloquent ORM  
✅ **XSS Protection** - Template escaping  

---

## 🌙 Dark Mode

- Toggle available in top-right corner
- Preference saved in browser's localStorage
- Persists across sessions
- Applies to all pages automatically

---

## 📱 Responsive Design

- **Mobile (< 640px)**: Single column, touch-optimized
- **Tablet (640-1024px)**: Two column layout
- **Desktop (> 1024px)**: Full multi-column interface
- **Large Desktop**: Extended sidebar

---

## 💡 Tips for Working with the Project

### During Development

1. **Always run `composer run dev`** instead of individual commands
   - This ensures web server, queue listener, Reverb, and Vite are all running
   - All 4 services are needed for full functionality

2. **Make small changes and test frequently**
   - After modifying a controller or service, refresh your browser
   - After modifying frontend files, Vite HMR will auto-refresh

3. **Check the terminal for errors**
   - The `composer run dev` terminal shows all 4 services' output
   - Look for error messages if something isn't working

4. **Clear caches when stuck**
   - Config cache can cause issues: `php artisan config:clear`
   - Route cache: `php artisan route:clear`
   - View cache: `php artisan view:clear`

### Adding New Features

1. Create a Model in `app/Models/`
2. Create a Migration in `database/migrations/`
3. Create a Service in `app/Services/` (for business logic)
4. Create a Controller in `app/Http/Controllers/`
5. Create FormRequest in `app/Http/Requests/` (for validation)
6. Add route in `routes/web.php`
7. Create view in `resources/views/`
8. Test with `composer run test`

### Modifying Database

1. Create a migration: `php artisan make:migration create_table_name`
2. Edit the migration file
3. Run migration: `php artisan migrate`
4. Update models as needed
5. Update tests if needed

---

## 🚨 Common Issues & Quick Fixes

| Problem | Solution |
|---------|----------|
| Page shows "welcome" instead of login | Make sure you're not logged in, or go to `/dashboard` |
| Real-time notifications not working | Check that Reverb is running (look for "Reverb server running" in terminal) |
| CSS/JS not loading properly | Run `npm run build` |
| Database locked | Close any other database connections, then `php artisan migrate:refresh` |
| Can't delete posts | Make sure you're logged in and you're the post author |
| Friends can't see your posts | The privacy system isn't implemented yet (all posts are public) |

---

## 📄 Additional Documentation

For more detailed information:
- **API Endpoints**: See [API_DOCUMENTATION.md](API_DOCUMENTATION.md)
- **Real-Time Setup**: See [REALTIME_NOTIFICATIONS.md](REALTIME_NOTIFICATIONS.md)
- **Laravel Docs**: https://laravel.com/docs
- **Tailwind CSS**: https://tailwindcss.com/docs

---

## 📄 License

The AGB platform is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

---

## ❓ Need Help?

1. Check the Troubleshooting section above
2. Read the Additional Documentation links
3. Check terminal output for error messages
4. Verify all prerequisites are installed correctly
5. Try clearing caches and rebuilding assets

**Happy coding! 🚀**
# AGB
