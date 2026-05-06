# Troubleshooting: Postman Returns HTML Instead of JSON

## The Problem
When you send a POST request to `http://localhost:8000/api/posts` in Postman, you get the Laravel welcome page (HTML) instead of a JSON response.

## Root Causes & Solutions

### 1. Wrong URL (Most Common)
**Problem:** You're hitting `http://localhost:8000/posts` instead of `http://localhost:8000/api/posts`

**Solution:**
- Make sure URL includes `/api` prefix
- Correct URL: `http://localhost:8000/api/posts`
- Wrong URL: `http://localhost:8000/posts`

### 2. Server Not Running
**Problem:** Laravel development server is not running

**Solution:**
```bash
cd backend
php artisan serve
```

You should see:
```
INFO  Server running on [http://127.0.0.1:8000].
```

### 3. Routes Not Loaded
**Problem:** Routes are cached or not loaded properly

**Solution:**
```bash
cd backend
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

### 4. Wrong HTTP Method
**Problem:** Using GET instead of POST

**Solution:**
- Make sure method is `POST` in Postman dropdown
- Not GET, PUT, or DELETE

### 5. Headers Issue
**Problem:** Content-Type header is set incorrectly

**Solution:**
- When using `form-data`, do NOT manually set Content-Type header
- Postman will automatically set it to `multipart/form-data`
- Remove any manual Content-Type headers

---

## Step-by-Step Diagnostic

### Test 1: Verify API is Working
**Request:**
- Method: `GET`
- URL: `http://localhost:8000/api/test`

**Expected Response:**
```json
{
    "message": "API is working"
}
```

If you get HTML here, your server is not running or URL is wrong.

---

### Test 2: Test Upload Endpoint
**Request:**
- Method: `POST`
- URL: `http://localhost:8000/api/test-upload`
- Body: `form-data`
  - title: "Test"
  - image: [Select a file]

**Expected Response:**
```json
{
    "message": "Test upload endpoint",
    "has_file": true,
    "all_inputs": {
        "title": "Test"
    },
    "files": {
        "image": {...}
    }
}
```

If this works but `/api/posts` doesn't, the issue is with authentication.

---

### Test 3: Verify Authentication
**Request:**
- Method: `POST`
- URL: `http://localhost:8000/api/login`
- Body: `raw` → `JSON`
```json
{
    "email": "ashu@example.com",
    "password": "password"
}
```

**Expected Response:**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "token": "1|..."
    }
}
```

Copy the token for next test.

---

### Test 4: Create Post with Token
**Request:**
- Method: `POST`
- URL: `http://localhost:8000/api/posts`
- Authorization: `Bearer Token` → paste token
- Body: `form-data`
  - title: "Test Post"
  - content: "Test content"

**Expected Response:**
```json
{
    "success": true,
    "message": "Post created successfully",
    "data": {...}
}
```

---

## Postman Configuration Checklist

### URL Tab
- [ ] URL is `http://localhost:8000/api/posts`
- [ ] Method is `POST`
- [ ] No typos in URL

### Authorization Tab
- [ ] Type is `Bearer Token`
- [ ] Token is pasted (without "Bearer" prefix)
- [ ] Token is valid (not expired)

### Headers Tab
- [ ] Do NOT manually add Content-Type
- [ ] Do NOT add Accept: application/json (optional but not required)
- [ ] Authorization header should be auto-added by Postman

### Body Tab
- [ ] Type is `form-data` (NOT raw, NOT x-www-form-urlencoded)
- [ ] title field is Text type
- [ ] content field is Text type
- [ ] image field is File type (click dropdown next to KEY)
- [ ] Image file is selected

---

## Still Not Working?

### Check Laravel Logs
```bash
cd backend
tail -f storage/logs/laravel.log
```

Send the request in Postman and watch for errors.

### Check Route List
```bash
cd backend
php artisan route:list --path=api/posts
```

You should see:
```
POST  api/posts .... Api\PostController@store
```

### Verify Database Connection
```bash
cd backend
php artisan tinker
```

Then run:
```php
\App\Models\User::count();
```

Should return a number, not an error.

---

## Quick Fix Commands

Run these commands in order:

```bash
cd backend
php artisan route:clear
php artisan config:clear
php artisan cache:clear
php artisan storage:link
php artisan serve
```

Then try Postman again with:
- URL: `http://localhost:8000/api/posts`
- Method: `POST`
- Auth: Bearer Token
- Body: form-data with title, content, and image
