# Quick Reference - Image Upload Testing

## 🚀 Quick Start (3 Steps)

### 1. Start Server
```bash
cd backend
php artisan serve
```

### 2. Login (Get Token)
**Postman:**
- POST `http://localhost:8000/api/login`
- Body: raw JSON
```json
{
    "email": "ashu@example.com",
    "password": "password"
}
```
- Copy the token from response

### 3. Create Post with Image
**Postman:**
- POST `http://localhost:8000/api/posts`
- Authorization: Bearer Token → paste token
- Body: form-data
  - title: "My Post" (Text)
  - content: "Content here" (Text)
  - image: [Select File] (File)
- Send

---

## 🔍 Troubleshooting (If Getting HTML)

### Quick Fix:
```bash
cd backend
php artisan route:clear
php artisan config:clear
php artisan serve
```

### Check URL:
- ✅ Correct: `http://localhost:8000/api/posts`
- ❌ Wrong: `http://localhost:8000/posts`

### Check Method:
- ✅ Correct: POST
- ❌ Wrong: GET

### Check Body Type:
- ✅ Correct: form-data
- ❌ Wrong: raw JSON

---

## 📝 Postman Configuration

### Authorization Tab:
```
Type: Bearer Token
Token: [paste your token here]
```

### Body Tab:
```
Type: form-data

KEY         VALUE                TYPE
title       My Post              Text
content     Content here         Text
image       [Select Files]       File ← Important!
```

**Note:** Click dropdown next to "image" and change to "File"

---

## ✅ Expected Response

```json
{
    "success": true,
    "message": "Post created successfully",
    "data": {
        "id": 1,
        "title": "My Post",
        "content": "Content here",
        "image": "posts/abc123.jpg",
        "image_url": "http://localhost:8000/storage/posts/abc123.jpg",
        ...
    }
}
```

Copy `image_url` and open in browser to see the image.

---

## 🧪 Test Commands

### Run All Tests:
```bash
cd backend
php artisan test --filter PostTest
```

### Test Specific:
```bash
php artisan test --filter "authenticated user can create a post with image"
```

### Check Routes:
```bash
php artisan route:list --path=api/posts
```

---

## 🆘 Common Errors

### "Unauthenticated" (401)
→ Token missing or invalid. Login again.

### "The title field is required" (422)
→ Fill title and content fields.

### "The image must be an image" (422)
→ Change image field type to "File" in Postman.

### Getting HTML welcome page
→ Check URL has `/api` prefix and run `php artisan route:clear`

---

## 📞 Quick Help

**Server not running?**
```bash
php artisan serve
```

**Routes not working?**
```bash
php artisan route:clear
php artisan config:clear
```

**Need fresh token?**
```bash
POST http://localhost:8000/api/login
```

**Test API working?**
```bash
GET http://localhost:8000/api/test
```

---

## 📚 Full Documentation

- `POSTMAN_EXACT_STEPS.md` - Detailed step-by-step guide
- `TROUBLESHOOTING.md` - Complete troubleshooting guide
- `IMAGE_UPLOAD_SUMMARY.md` - Full implementation details
- `POSTMAN_IMAGE_UPLOAD_GUIDE.md` - Comprehensive Postman guide

---

**Status:** ✅ Backend ready, ⚠️ Needs Postman verification
