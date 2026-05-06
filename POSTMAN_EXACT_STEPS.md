# EXACT POSTMAN STEPS - Image Upload

## Before You Start

1. **Make sure server is running:**
```bash
cd backend
php artisan serve
```

You should see: `Server running on [http://127.0.0.1:8000]`

2. **Clear all caches:**
```bash
php artisan route:clear
php artisan config:clear
php artisan cache:clear
```

---

## STEP 1: Login to Get Token

### Postman Configuration:
1. Create new request
2. Set method to: **POST**
3. Set URL to: `http://localhost:8000/api/login`
4. Go to **Body** tab
5. Select **raw**
6. Select **JSON** from dropdown
7. Paste this:
```json
{
    "email": "ashu@example.com",
    "password": "password"
}
```
8. Click **Send**

### Expected Response:
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "Ashu",
            "email": "ashu@example.com"
        },
        "token": "1|abcdefghijklmnopqrstuvwxyz123456789"
    }
}
```

9. **COPY THE TOKEN** (the long string after "token":)

---

## STEP 2: Create Post WITHOUT Image (Test First)

### Postman Configuration:
1. Create new request
2. Set method to: **POST**
3. Set URL to: `http://localhost:8000/api/posts`
4. Go to **Authorization** tab
5. Select **Bearer Token** from Type dropdown
6. Paste your token in the Token field
7. Go to **Body** tab
8. Select **form-data** (NOT raw!)
9. Add these fields (all as Text):

| KEY | VALUE |
|-----|-------|
| title | My First Post |
| content | This is test content |

10. Click **Send**

### Expected Response:
```json
{
    "success": true,
    "message": "Post created successfully",
    "data": {
        "id": 1,
        "title": "My First Post",
        "content": "This is test content",
        "user_id": 1,
        "image": null,
        "image_url": null,
        ...
    }
}
```

### If You Get HTML Instead:
- Check URL is exactly: `http://localhost:8000/api/posts` (with /api)
- Check method is POST
- Check token is in Authorization tab
- Run: `php artisan route:clear` and try again

---

## STEP 3: Create Post WITH Image

### Postman Configuration:
1. Use same request from Step 2
2. Keep Authorization tab with Bearer Token
3. Go to **Body** tab
4. Keep **form-data** selected
5. Add these fields:

| KEY | VALUE | TYPE |
|-----|-------|------|
| title | Post with Image | Text |
| content | This post has an image | Text |
| image | [Click Select Files] | **File** |

**IMPORTANT:** 
- Click the dropdown next to "image" KEY
- Change from "Text" to "File"
- Then click "Select Files" button that appears
- Choose a JPG or PNG image (less than 2MB)

6. Click **Send**

### Expected Response:
```json
{
    "success": true,
    "message": "Post created successfully",
    "data": {
        "id": 2,
        "title": "Post with Image",
        "content": "This post has an image",
        "user_id": 1,
        "image": "posts/abc123xyz.jpg",
        "image_url": "http://localhost:8000/storage/posts/abc123xyz.jpg",
        ...
    }
}
```

7. **Copy the image_url** and paste it in your browser
8. You should see the uploaded image!

---

## STEP 4: Create Post with Image + Category + Tags

### Prerequisites:
First create a category and tag:

**Create Category:**
- POST `http://localhost:8000/api/categories`
- Authorization: Bearer Token
- Body: form-data
  - name: "Technology"

**Create Tag:**
- POST `http://localhost:8000/api/tags`
- Authorization: Bearer Token
- Body: form-data
  - name: "Laravel"

### Create Post with Everything:

| KEY | VALUE | TYPE |
|-----|-------|------|
| title | Complete Post | Text |
| content | Post with everything | Text |
| category_id | 1 | Text |
| tags[0] | 1 | Text |
| image | [Select File] | File |

---

## Common Errors & Solutions

### Error: "Unauthenticated" (401)
**Solution:** 
- Token is missing or invalid
- Get a new token by logging in again
- Make sure token is in Authorization tab, not Headers

### Error: "The title field is required" (422)
**Solution:**
- Make sure title and content are filled
- Check spelling of field names

### Error: "The image must be an image" (422)
**Solution:**
- Make sure image field type is "File" not "Text"
- Select a valid JPG/PNG file
- File size must be less than 2MB

### Error: Getting HTML welcome page
**Solution:**
1. Check URL has `/api` prefix
2. Run: `php artisan route:clear`
3. Make sure server is running
4. Try the test endpoint first: GET `http://localhost:8000/api/test`

---

## Verification Checklist

Before sending request, verify:

- [ ] Server is running (`php artisan serve`)
- [ ] URL is `http://localhost:8000/api/posts`
- [ ] Method is `POST`
- [ ] Authorization tab has Bearer Token with valid token
- [ ] Body tab is set to `form-data`
- [ ] title field exists (Text type)
- [ ] content field exists (Text type)
- [ ] image field type is `File` (not Text)
- [ ] Image file is selected
- [ ] No manual Content-Type header in Headers tab

---

## Screenshot Guide

### Body Tab Should Look Like:
```
○ none
○ form-data  ← Selected
○ x-www-form-urlencoded
○ raw
○ binary
○ GraphQL

┌─────────────┬──────────────────────┬──────────┐
│ KEY         │ VALUE                │ TYPE     │
├─────────────┼──────────────────────┼──────────┤
│ title       │ Post with Image      │ Text     │
│ content     │ This has an image    │ Text     │
│ image       │ [Select Files]       │ File ▼   │
└─────────────┴──────────────────────┴──────────┘
```

### Authorization Tab Should Look Like:
```
Type: Bearer Token ▼

Token: 1|abcdefghijklmnopqrstuvwxyz123456789
```

---

## Need Help?

If still not working, run this diagnostic:

```bash
cd backend
php artisan route:list --path=api/posts
```

Should show:
```
POST  api/posts .... Api\PostController@store
```

If not showing, run:
```bash
php artisan route:clear
php artisan config:clear
```

Then check again.
