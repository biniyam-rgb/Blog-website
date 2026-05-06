# Search & Filter - Quick Reference

## 🚀 Quick Start

### Get All Posts
```
GET /api/posts
```

### Search
```
GET /api/posts?search=laravel
```

### Filter by Category
```
GET /api/posts?category=1
```

### Filter by Tag
```
GET /api/posts?tag=2
```

### Combine All
```
GET /api/posts?search=tutorial&category=1&tag=2
```

### Pagination
```
GET /api/posts?page=2
```

---

## 📋 Query Parameters

| Parameter | Type | Example |
|-----------|------|---------|
| search | string | `?search=laravel` |
| category | integer | `?category=1` |
| tag | integer | `?tag=2` |
| page | integer | `?page=2` |

---

## 🧪 Test Commands

### Run All Tests
```bash
php artisan test --filter PostTest
```

### Run Search Tests Only
```bash
php artisan test --filter "can search"
```

### Run Filter Tests Only
```bash
php artisan test --filter "can filter"
```

---

## 📊 Response Format

```json
{
    "success": true,
    "message": "Posts retrieved successfully",
    "data": [...],
    "pagination": {
        "total": 25,
        "per_page": 10,
        "current_page": 1,
        "last_page": 3,
        "from": 1,
        "to": 10
    }
}
```

---

## ✅ Features

- ✅ Search by title
- ✅ Search by content
- ✅ Filter by category
- ✅ Filter by tag
- ✅ Combine filters
- ✅ Pagination (10/page)
- ✅ Latest first
- ✅ Eager loading

---

## 🎯 Common Use Cases

### Homepage
```
GET /api/posts
```

### Search Bar
```
GET /api/posts?search={user_input}
```

### Category Page
```
GET /api/posts?category={id}
```

### Tag Page
```
GET /api/posts?tag={id}
```

### Advanced Search
```
GET /api/posts?search={q}&category={c}&tag={t}
```

---

## 🧪 Postman Examples

### 1. Search
- URL: `http://localhost:8000/api/posts?search=laravel`
- Method: GET
- No auth required

### 2. Filter by Category
- URL: `http://localhost:8000/api/posts?category=1`
- Method: GET
- No auth required

### 3. Filter by Tag
- URL: `http://localhost:8000/api/posts?tag=2`
- Method: GET
- No auth required

### 4. Combined
- URL: `http://localhost:8000/api/posts?search=react&category=1&tag=2`
- Method: GET
- No auth required

---

## 📝 Test Results

**17/17 tests passing** ✅

New tests added:
- ✓ can search posts by title
- ✓ can search posts by content
- ✓ can filter posts by category
- ✓ can filter posts by tag
- ✓ can combine search and filters
- ✓ posts are paginated
- ✓ can navigate to second page
- ✓ posts are sorted by latest first

---

## 🔧 Code Location

**Controller:** `app/Http/Controllers/Api/PostController.php`  
**Method:** `index()`  
**Tests:** `tests/Feature/PostTest.php`  
**Route:** `GET /api/posts` (public)

---

**Status:** ✅ Complete and tested  
**Ready for:** Production use
