# Git Push Guide - Separate Commits for Each Feature

Follow these commands to push each feature separately to GitHub.

---

## Commit 1: Database Migration for Image Upload

Add the migration file:

```bash
cd backend
git add database/migrations/2026_05_06_221341_add_image_to_posts_table.php
git commit -m "feat: add image column to posts table

- Add nullable image column to posts table
- Migration for storing image file paths
- Supports JPG, JPEG, PNG image uploads"
```

---

## Commit 2: Update Post Model for Image Support

Add the model changes:

```bash
git add app/Models/Post.php
git commit -m "feat: add image support to Post model

- Add image to fillable fields
- Add image_url accessor for full URL generation
- Append image_url to JSON responses
- Support for image file path storage"
```

---

## Commit 3: Implement Image Upload in PostController

Add the controller changes:

```bash
git add app/Http/Controllers/Api/PostController.php
git commit -m "feat: implement image upload functionality in PostController

- Add image validation (jpg, jpeg, png, max 2MB)
- Store uploaded images in storage/app/public/posts/
- Handle image upload in store() method
- Handle image replacement in update() method
- Delete image file when post is deleted
- Return image_url in API responses"
```

---

## Commit 4: Add Image Upload Tests

Add the test changes:

```bash
git add tests/Feature/PostTest.php
git commit -m "test: add image upload tests

- Test creating post with image
- Test image file storage verification
- Test image deletion when post is deleted
- Test image_url in response
- All tests passing (21/21)"
```

---

## Commit 5: Add Image Upload Documentation

Add all documentation files:

```bash
git add QUICK_REFERENCE.md
git add POSTMAN_VISUAL_GUIDE.txt
git add POSTMAN_EXACT_STEPS.md
git add POSTMAN_IMAGE_UPLOAD_GUIDE.md
git add TROUBLESHOOTING.md
git add IMAGE_UPLOAD_SUMMARY.md
git add README_IMAGE_UPLOAD.md
git add TESTING_CHECKLIST.md
git add TEST_RESULTS.md
git add TEST_SUMMARY.txt
git add DOCUMENTATION_INDEX.md
git commit -m "docs: add comprehensive image upload documentation

- Quick reference guide for testing
- Visual Postman setup guide
- Step-by-step Postman instructions
- Troubleshooting guide for common issues
- Complete implementation summary
- Testing checklist with 13 scenarios
- Test results documentation
- Documentation index for easy navigation"
```

---

## Push All Commits to GitHub

Push all commits at once:

```bash
git push origin main
```

Or push each commit separately (if you want to verify each one):

```bash
# After each commit above, run:
git push origin main
```

---

## Verify on GitHub

After pushing, verify on GitHub:

1. Go to your repository
2. Check the commits tab
3. You should see 5 separate commits:
   - feat: add image column to posts table
   - feat: add image support to Post model
   - feat: implement image upload functionality in PostController
   - test: add image upload tests
   - docs: add comprehensive image upload documentation

---

## Alternative: Single Command for All

If you want to do everything in one go:

```bash
cd backend

# Commit 1: Migration
git add database/migrations/2026_05_06_221341_add_image_to_posts_table.php
git commit -m "feat: add image column to posts table"

# Commit 2: Model
git add app/Models/Post.php
git commit -m "feat: add image support to Post model"

# Commit 3: Controller
git add app/Http/Controllers/Api/PostController.php
git commit -m "feat: implement image upload functionality in PostController"

# Commit 4: Tests
git add tests/Feature/PostTest.php
git commit -m "test: add image upload tests"

# Commit 5: Documentation
git add QUICK_REFERENCE.md POSTMAN_VISUAL_GUIDE.txt POSTMAN_EXACT_STEPS.md POSTMAN_IMAGE_UPLOAD_GUIDE.md TROUBLESHOOTING.md IMAGE_UPLOAD_SUMMARY.md README_IMAGE_UPLOAD.md TESTING_CHECKLIST.md TEST_RESULTS.md TEST_SUMMARY.txt DOCUMENTATION_INDEX.md
git commit -m "docs: add comprehensive image upload documentation"

# Push all
git push origin main
```

---

## Commit Message Format

I'm using conventional commits format:

- `feat:` - New feature
- `test:` - Adding tests
- `docs:` - Documentation only
- `fix:` - Bug fix
- `refactor:` - Code refactoring

---

## Summary of Changes

### Files Modified (3):
1. `app/Models/Post.php`
2. `app/Http/Controllers/Api/PostController.php`
3. `tests/Feature/PostTest.php`

### Files Added (12):
1. `database/migrations/2026_05_06_221341_add_image_to_posts_table.php`
2. `QUICK_REFERENCE.md`
3. `POSTMAN_VISUAL_GUIDE.txt`
4. `POSTMAN_EXACT_STEPS.md`
5. `POSTMAN_IMAGE_UPLOAD_GUIDE.md`
6. `TROUBLESHOOTING.md`
7. `IMAGE_UPLOAD_SUMMARY.md`
8. `README_IMAGE_UPLOAD.md`
9. `TESTING_CHECKLIST.md`
10. `TEST_RESULTS.md`
11. `TEST_SUMMARY.txt`
12. `DOCUMENTATION_INDEX.md`

### Total: 15 files (3 modified + 12 new)

---

## After Pushing

Once pushed, you can:

1. ✅ View commits on GitHub
2. ✅ Share the repository
3. ✅ Create a release/tag
4. ✅ Continue with frontend development

---

## Need Help?

If you encounter any issues:

1. Check git status: `git status`
2. Check git log: `git log --oneline`
3. Check remote: `git remote -v`
4. Force push (if needed): `git push -f origin main` (use carefully!)

---

**Ready to push!** 🚀

Just copy and paste the commands above in your terminal.
