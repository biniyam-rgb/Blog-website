# Postman Image Upload Guide

## Prerequisites
1. Laravel server must be running: `php artisan serve`
2. You must have a valid authentication token

## Step 1: Get Authentication Token

### Option A: Use existing token from previous login
If you already logged in, use that token.

### Option B: Login to get new token
**Request:**
- Method: `POST`
- URL: `http://localhost:8000/api/login`
- Body Type: `raw` → `JSON`
- Body:
```json
{
    "email": "ashu@example.com",
    "password": "password"
}
```

**Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {...},
        "token": "1|abcdefghijklmnopqrstuvwxyz..."
    }
}
```

Copy the token value.

---

## Step 2: Create Post with Image

**Request:**
- Method: `POST`
- URL: `http://localhost:8000/api/posts`
- Authorization Tab:
  - Type: `Bearer Token`
  - Token: `paste your token here`
- Body Tab:
  - Select `form-data` (NOT raw JSON!)
  - Add fields:

| KEY | VALUE | TYPE |
|-----|-------|------|
| title | My Post with Image | Text |
| content | This is a test post with an image | Text |
| image | [Select File] | File |

**Important:**
- Click on the dropdown next to KEY and change `image` to `File` type
- Click "Select Files" and choose a JPG/PNG image (max 2MB)

**Expected Response:**
```json
{
    "success": true,
    "message": "Post created successfully",
    "data": {
        "id": 1,
        "title": "My Post with Image",
        "content": "This is a test post with an image",
        "user_id": 1,
        "category_id": null,
        "image": "posts/abc123.jpg",
        "image_url": "http://localhost:8000/storage/posts/abc123.jpg",
        "created_at": "2026-05-07T...",
        "updated_at": "2026-05-07T...",
        "user": {...},
        "category": null,
        "tags": []
    }
}
```

---

## Step 3: Verify Image Upload

1. Check the response has `image` and `image_url` fields
2. Copy the `image_url` and paste it in your browser
3. You should see the uploaded image

---

## Common Issues & Solutions

### Issue 1: Getting HTML welcome page instead of JSON
**Cause:** Wrong URL or missing `/api` prefix
**Solution:** 
- Make sure URL is: `http://localhost:8000/api/posts` (not `http://localhost:8000/posts`)
- Clear routes: `php artisan route:clear`
- Clear config: `php artisan config:clear`

### Issue 2: 401 Unauthorized
**Cause:** Missing or invalid token
**Solution:**
- Make sure you added the token in Authorization tab
- Token format: `Bearer Token` type (not in headers manually)
- Get a fresh token by logging in again

### Issue 3: 422 Validation Error
**Cause:** Missing required fields or wrong image format
**Solution:**
- Make sure `title` and `content` are provided
- Image must be JPG, JPEG, or PNG
- Image size must be less than 2MB

### Issue 4: Image not displaying
**Cause:** Storage link not created
**Solution:**
```bash
php artisan storage:link
```

---

## Optional: Add Category and Tags

You can also add category and tags to the post:

| KEY | VALUE | TYPE |
|-----|-------|------|
| title | My Post | Text |
| content | Content here | Text |
| category_id | 1 | Text |
| tags[0] | 1 | Text |
| tags[1] | 2 | Text |
| image | [Select File] | File |

---

## Testing Checklist

- [ ] Server is running (`php artisan serve`)
- [ ] Storage link created (`php artisan storage:link`)
- [ ] Routes cleared (`php artisan route:clear`)
- [ ] Valid token obtained from login
- [ ] Postman URL is `http://localhost:8000/api/posts`
- [ ] Authorization tab has Bearer Token
- [ ] Body type is `form-data` (not JSON)
- [ ] Image field type is `File` (not Text)
- [ ] Response is JSON (not HTML)
