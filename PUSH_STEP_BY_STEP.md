# Push to GitHub - Step by Step (Separate Commits)

Follow these steps **one by one**. After each step, you'll have a new commit on GitHub.

---

## ✅ STEP 1: Push Migration

```bash
cd backend
git add database/migrations/2026_05_06_221341_add_image_to_posts_table.php
git commit -m "feat: add image column to posts table"
git push origin main
```

**Wait for push to complete, then continue to Step 2.**

---

## ✅ STEP 2: Push Model Changes

```bash
git add app/Models/Post.php
git commit -m "feat: add image support to Post model"
git push origin main
```

**Wait for push to complete, then continue to Step 3.**

---

## ✅ STEP 3: Push Controller Changes

```bash
git add app/Http/Controllers/Api/PostController.php
git commit -m "feat: implement image upload in PostController"
git push origin main
```

**Wait for push to complete, then continue to Step 4.**

---

## ✅ STEP 4: Push Tests

```bash
git add tests/Feature/PostTest.php
git commit -m "test: add image upload tests"
git push origin main
```

**Wait for push to complete, then continue to Step 5.**

---

## ✅ STEP 5: Push Documentation (Part 1)

```bash
git add QUICK_REFERENCE.md
git add POSTMAN_VISUAL_GUIDE.txt
git add POSTMAN_EXACT_STEPS.md
git commit -m "docs: add Postman testing guides"
git push origin main
```

**Wait for push to complete, then continue to Step 6.**

---

## ✅ STEP 6: Push Documentation (Part 2)

```bash
git add POSTMAN_IMAGE_UPLOAD_GUIDE.md
git add TROUBLESHOOTING.md
git add IMAGE_UPLOAD_SUMMARY.md
git commit -m "docs: add troubleshooting and summary guides"
git push origin main
```

**Wait for push to complete, then continue to Step 7.**

---

## ✅ STEP 7: Push Documentation (Part 3)

```bash
git add README_IMAGE_UPLOAD.md
git add TESTING_CHECKLIST.md
git add TEST_RESULTS.md
git commit -m "docs: add testing documentation"
git push origin main
```

**Wait for push to complete, then continue to Step 8.**

---

## ✅ STEP 8: Push Documentation (Part 4)

```bash
git add TEST_SUMMARY.txt
git add DOCUMENTATION_INDEX.md
git add GIT_PUSH_GUIDE.md
git commit -m "docs: add test summary and documentation index"
git push origin main
```

**Wait for push to complete. Done!**

---

## 📊 Summary

After completing all steps, you will have **8 separate commits** on GitHub:

1. ✅ feat: add image column to posts table
2. ✅ feat: add image support to Post model
3. ✅ feat: implement image upload in PostController
4. ✅ test: add image upload tests
5. ✅ docs: add Postman testing guides
6. ✅ docs: add troubleshooting and summary guides
7. ✅ docs: add testing documentation
8. ✅ docs: add test summary and documentation index

---

## 🔍 Verify After Each Push

After each `git push`, you can verify on GitHub:

1. Go to: https://github.com/YOUR_USERNAME/YOUR_REPO
2. Click on "commits"
3. You should see the new commit

---

## ⚠️ Important Notes

- Run commands **one by one**
- Wait for each push to complete before the next one
- If you get an error, stop and check the error message
- You can check status anytime with: `git status`

---

## 🆘 If You Get Errors

### Error: "nothing to commit"
→ You already committed that file. Skip to next step.

### Error: "failed to push"
→ Run: `git pull origin main` then try push again

### Error: "permission denied"
→ Check your GitHub authentication

---

## ✨ Alternative: Copy All Commands at Once

If you want to run all at once (but still create separate commits):

```bash
cd backend

# Step 1
git add database/migrations/2026_05_06_221341_add_image_to_posts_table.php
git commit -m "feat: add image column to posts table"
git push origin main

# Step 2
git add app/Models/Post.php
git commit -m "feat: add image support to Post model"
git push origin main

# Step 3
git add app/Http/Controllers/Api/PostController.php
git commit -m "feat: implement image upload in PostController"
git push origin main

# Step 4
git add tests/Feature/PostTest.php
git commit -m "test: add image upload tests"
git push origin main

# Step 5
git add QUICK_REFERENCE.md POSTMAN_VISUAL_GUIDE.txt POSTMAN_EXACT_STEPS.md
git commit -m "docs: add Postman testing guides"
git push origin main

# Step 6
git add POSTMAN_IMAGE_UPLOAD_GUIDE.md TROUBLESHOOTING.md IMAGE_UPLOAD_SUMMARY.md
git commit -m "docs: add troubleshooting and summary guides"
git push origin main

# Step 7
git add README_IMAGE_UPLOAD.md TESTING_CHECKLIST.md TEST_RESULTS.md
git commit -m "docs: add testing documentation"
git push origin main

# Step 8
git add TEST_SUMMARY.txt DOCUMENTATION_INDEX.md GIT_PUSH_GUIDE.md PUSH_STEP_BY_STEP.md
git commit -m "docs: add test summary and documentation index"
git push origin main
```

---

**Ready to start!** 🚀

Begin with **STEP 1** above.
