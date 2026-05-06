# Image Upload Testing Checklist

Use this checklist to verify the image upload system is working correctly.

---

## ✅ Pre-Testing Setup

### Server Setup
- [ ] Navigate to backend directory: `cd backend`
- [ ] Clear routes: `php artisan route:clear`
- [ ] Clear config: `php artisan config:clear`
- [ ] Clear cache: `php artisan cache:clear`
- [ ] Start server: `php artisan serve`
- [ ] Server shows: "Server running on [http://127.0.0.1:8000]"

### Storage Setup
- [ ] Storage link exists: `public/storage` → `storage/app/public`
- [ ] If not, run: `php artisan storage:link`

### Database Setup
- [ ] Database connection working
- [ ] Users table has data (at least one user)
- [ ] Posts table exists with `image` column

---

## ✅ Pest Tests

Run all tests:
```bash
php artisan test
```

### Expected Results:
- [ ] All tests pass (21/21)
- [ ] "authenticated user can create a post with image" ✓
- [ ] "post image is deleted when post is deleted" ✓
- [ ] No errors or failures

---

## ✅ Postman Testing

### Test 1: API Health Check
- [ ] Method: GET
- [ ] URL: `http://localhost:8000/api/test`
- [ ] Expected: `{"message": "API is working"}`
- [ ] Status: 200 OK
- [ ] Response is JSON (not HTML)

**If this fails, server is not running or URL is wrong.**

---

### Test 2: Login
- [ ] Method: POST
- [ ] URL: `http://localhost:8000/api/login`
- [ ] Body type: raw → JSON
- [ ] Body contains:
  ```json
  {
      "email": "ashu@example.com",
      "password": "password"
  }
  ```
- [ ] Click Send
- [ ] Status: 200 OK
- [ ] Response has `success: true`
- [ ] Response has `data.token`
- [ ] Token copied to clipboard

**If this fails, check email/password or database.**

---

### Test 3: Create Post WITHOUT Image
- [ ] Method: POST
- [ ] URL: `http://localhost:8000/api/posts`
- [ ] Authorization tab: Bearer Token
- [ ] Token pasted in Token field
- [ ] Body type: form-data
- [ ] Fields added:
  - [ ] title: "Test Post" (Text)
  - [ ] content: "Test content" (Text)
- [ ] Click Send
- [ ] Status: 201 Created
- [ ] Response has `success: true`
- [ ] Response has `data.id`
- [ ] Response has `data.title`
- [ ] `data.image` is null
- [ ] `data.image_url` is null

**If this fails, check token or URL has /api prefix.**

---

### Test 4: Create Post WITH Image
- [ ] Method: POST
- [ ] URL: `http://localhost:8000/api/posts`
- [ ] Authorization tab: Bearer Token
- [ ] Token pasted in Token field
- [ ] Body type: form-data
- [ ] Fields added:
  - [ ] title: "Post with Image" (Text)
  - [ ] content: "This has an image" (Text)
  - [ ] image: [File selected] (File)
- [ ] Image field type is "File" (not "Text")
- [ ] Image file is JPG/PNG, less than 2MB
- [ ] Click Send
- [ ] Status: 201 Created
- [ ] Response has `success: true`
- [ ] Response has `data.image` (not null)
- [ ] Response has `data.image_url` (not null)
- [ ] `image_url` starts with `http://localhost:8000/storage/posts/`

**If this fails, check image field type is "File".**

---

### Test 5: Verify Image URL
- [ ] Copy `image_url` from previous response
- [ ] Paste URL in browser address bar
- [ ] Press Enter
- [ ] Image displays in browser
- [ ] Image is the one you uploaded

**If this fails, storage link may be missing.**

---

### Test 6: Verify Image File Exists
- [ ] Navigate to: `backend/storage/app/public/posts/`
- [ ] Image file exists in this directory
- [ ] Filename matches the one in `data.image`

---

### Test 7: Create Post with Image + Category + Tags

#### 7a: Create Category
- [ ] Method: POST
- [ ] URL: `http://localhost:8000/api/categories`
- [ ] Authorization: Bearer Token
- [ ] Body: form-data
  - [ ] name: "Technology" (Text)
- [ ] Status: 201 Created
- [ ] Note the category ID

#### 7b: Create Tag
- [ ] Method: POST
- [ ] URL: `http://localhost:8000/api/tags`
- [ ] Authorization: Bearer Token
- [ ] Body: form-data
  - [ ] name: "Laravel" (Text)
- [ ] Status: 201 Created
- [ ] Note the tag ID

#### 7c: Create Post with Everything
- [ ] Method: POST
- [ ] URL: `http://localhost:8000/api/posts`
- [ ] Authorization: Bearer Token
- [ ] Body: form-data
  - [ ] title: "Complete Post" (Text)
  - [ ] content: "Has everything" (Text)
  - [ ] category_id: 1 (Text)
  - [ ] tags[0]: 1 (Text)
  - [ ] image: [File] (File)
- [ ] Status: 201 Created
- [ ] Response has `data.category` (not null)
- [ ] Response has `data.tags` (array with 1 item)
- [ ] Response has `data.image_url` (not null)

---

### Test 8: Update Post Image
- [ ] Method: PUT
- [ ] URL: `http://localhost:8000/api/posts/1` (use existing post ID)
- [ ] Authorization: Bearer Token
- [ ] Body: form-data
  - [ ] title: "Updated Post" (Text)
  - [ ] content: "Updated content" (Text)
  - [ ] image: [New File] (File)
- [ ] Status: 200 OK
- [ ] Response has new `image_url`
- [ ] Old image file deleted from storage
- [ ] New image file exists in storage

---

### Test 9: Delete Post with Image
- [ ] Note the image filename from a post
- [ ] Method: DELETE
- [ ] URL: `http://localhost:8000/api/posts/1` (use post with image)
- [ ] Authorization: Bearer Token
- [ ] Status: 200 OK
- [ ] Response has `success: true`
- [ ] Check `storage/app/public/posts/` directory
- [ ] Image file is deleted

---

## ✅ Error Testing

### Test 10: Unauthenticated Request
- [ ] Method: POST
- [ ] URL: `http://localhost:8000/api/posts`
- [ ] No Authorization header
- [ ] Body: form-data (title, content)
- [ ] Status: 401 Unauthorized
- [ ] Response has error message

---

### Test 11: Invalid Image Type
- [ ] Method: POST
- [ ] URL: `http://localhost:8000/api/posts`
- [ ] Authorization: Bearer Token
- [ ] Body: form-data
  - [ ] title: "Test" (Text)
  - [ ] content: "Test" (Text)
  - [ ] image: [PDF or TXT file] (File)
- [ ] Status: 422 Unprocessable Entity
- [ ] Response has validation error for image

---

### Test 12: Image Too Large
- [ ] Method: POST
- [ ] URL: `http://localhost:8000/api/posts`
- [ ] Authorization: Bearer Token
- [ ] Body: form-data
  - [ ] title: "Test" (Text)
  - [ ] content: "Test" (Text)
  - [ ] image: [File > 2MB] (File)
- [ ] Status: 422 Unprocessable Entity
- [ ] Response has validation error for image size

---

### Test 13: Missing Required Fields
- [ ] Method: POST
- [ ] URL: `http://localhost:8000/api/posts`
- [ ] Authorization: Bearer Token
- [ ] Body: form-data
  - [ ] image: [File] (File)
  - [ ] (no title or content)
- [ ] Status: 422 Unprocessable Entity
- [ ] Response has validation errors for title and content

---

## ✅ Final Verification

### Code Review
- [ ] `Post` model has `image` in `$fillable`
- [ ] `Post` model has `image_url` accessor
- [ ] `PostController` validates image
- [ ] `PostController` stores image on create
- [ ] `PostController` deletes old image on update
- [ ] `PostController` deletes image on destroy
- [ ] Routes are registered in `api.php`

### Database
- [ ] Posts table has `image` column
- [ ] Column is nullable
- [ ] Column type is string

### Storage
- [ ] `storage/app/public/posts/` directory exists
- [ ] `public/storage` symlink exists
- [ ] Uploaded images are in `posts/` directory

### Documentation
- [ ] README_IMAGE_UPLOAD.md exists
- [ ] QUICK_REFERENCE.md exists
- [ ] POSTMAN_VISUAL_GUIDE.txt exists
- [ ] TROUBLESHOOTING.md exists
- [ ] All guides are clear and helpful

---

## 📊 Summary

### Tests Completed: _____ / 13
### All Pest Tests Passing: ☐ Yes ☐ No
### Image Upload Working: ☐ Yes ☐ No
### Image Display Working: ☐ Yes ☐ No
### Image Deletion Working: ☐ Yes ☐ No

---

## 🎯 Success Criteria

The image upload system is working correctly if:

1. ✅ All 21 Pest tests pass
2. ✅ Can create post with image in Postman
3. ✅ Response includes `image` and `image_url`
4. ✅ Image URL opens in browser
5. ✅ Image file exists in storage
6. ✅ Updating post replaces image
7. ✅ Deleting post removes image
8. ✅ Validation prevents invalid files
9. ✅ Works with categories and tags
10. ✅ Authorization works correctly

---

## 🆘 If Any Test Fails

1. Check the specific test section above
2. Read TROUBLESHOOTING.md for that issue
3. Verify server is running
4. Clear caches: `php artisan route:clear && php artisan config:clear`
5. Check Laravel logs: `tail -f storage/logs/laravel.log`
6. Verify routes: `php artisan route:list --path=api/posts`

---

**Testing Date:** _____________  
**Tested By:** _____________  
**Result:** ☐ Pass ☐ Fail  
**Notes:** _____________________________________________

---

Good luck with testing! 🚀
