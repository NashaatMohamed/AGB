# AGB Social Network API Documentation

Base URL: `http://localhost:8000/api`

## Authentication

All protected endpoints require a Bearer token in the Authorization header:
```
Authorization: Bearer {your_access_token}
```

### Register
**POST** `/api/register`

Request:
```json
{
  "name": "John Doe",
  "email": "john@example.com",
  "password": "password123",
  "password_confirmation": "password123"
}
```

Response (201):
```json
{
  "success": true,
  "message": "User registered successfully",
  "data": {
    "user": {...},
    "access_token": "1|abc123...",
    "token_type": "Bearer"
  }
}
```

### Login
**POST** `/api/login`

Request:
```json
{
  "email": "john@example.com",
  "password": "password123"
}
```

Response (200):
```json
{
  "success": true,
  "message": "Login successful",
  "data": {
    "user": {...},
    "access_token": "2|def456...",
    "token_type": "Bearer"
  }
}
```

### Logout
**POST** `/api/logout`

Requires: Authentication

Response (200):
```json
{
  "success": true,
  "message": "Logged out successfully"
}
```

### Get Current User
**GET** `/api/user`

Requires: Authentication

Response (200):
```json
{
  "success": true,
  "data": {
    "id": 1,
    "name": "John Doe",
    "email": "john@example.com",
    "profile": {...}
  }
}
```

## Users

### Get User Profile
**GET** `/api/profile`

Requires: Authentication

### Update Profile
**PUT** `/api/profile`

Requires: Authentication

Request (multipart/form-data):
- `name` (optional)
- `email` (optional)
- `bio` (optional)
- `profile_picture` (optional, file)

### Get User by ID
**GET** `/api/users/{user}`

Requires: Authentication

### Search Users
**GET** `/api/users/search?q={query}`

Requires: Authentication

## Posts

### List Posts (News Feed)
**GET** `/api/posts`

Requires: Authentication

Returns paginated posts from friends and self.

### Create Post
**POST** `/api/posts`

Requires: Authentication

Request (multipart/form-data):
- `content` (required, max 5000)
- `image` (optional, file, max 5MB)

### Get Post
**GET** `/api/posts/{post}`

Requires: Authentication

### Update Post
**PUT/PATCH** `/api/posts/{post}`

Requires: Authentication (owner only)

### Delete Post
**DELETE** `/api/posts/{post}`

Requires: Authentication (owner only)

## Comments

### List Comments
**GET** `/api/posts/{post}/comments`

Requires: Authentication

### Add Comment
**POST** `/api/posts/{post}/comments`

Requires: Authentication

Request:
```json
{
  "content": "Great post!"
}
```

### Update Comment
**PUT** `/api/comments/{comment}`

Requires: Authentication (owner only)

### Delete Comment
**DELETE** `/api/comments/{comment}`

Requires: Authentication (owner only)

## Likes

### Toggle Like
**POST** `/api/posts/{post}/like`

Requires: Authentication

Response:
```json
{
  "success": true,
  "message": "Post liked",
  "data": {
    "liked": true,
    "likes_count": 5
  }
}
```

### Get Liked By Users
**GET** `/api/posts/{post}/likes`

Requires: Authentication

## Friendships

### List Friends
**GET** `/api/friends`

Requires: Authentication

Returns friends, pending requests (received), and sent requests.

### Send Friend Request
**POST** `/api/friends/{user}/request`

Requires: Authentication

### Accept Friend Request
**PUT** `/api/friendships/{friendship}/accept`

Requires: Authentication (receiver only)

### Reject Friend Request
**PUT** `/api/friendships/{friendship}/reject`

Requires: Authentication (receiver only)

### Remove Friend
**DELETE** `/api/friends/{friend}`

Requires: Authentication

## Response Format

All API responses follow this format:

Success:
```json
{
  "success": true,
  "message": "Operation successful",
  "data": {...}
}
```

Error:
```json
{
  "success": false,
  "message": "Error message",
  "errors": {...}
}
```

## Testing with cURL

```bash
# Register
curl -X POST http://localhost:8000/api/register \
  -H "Content-Type: application/json" \
  -d '{"name":"John","email":"john@test.com","password":"password","password_confirmation":"password"}'

# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -d '{"email":"john@test.com","password":"password"}'

# Get Posts (with token)
curl -X GET http://localhost:8000/api/posts \
  -H "Authorization: Bearer YOUR_TOKEN"
```
