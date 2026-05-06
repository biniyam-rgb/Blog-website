# Search & Filtering System - Complete Guide

## ✅ Implementation Complete

The search and filtering system has been successfully implemented for blog posts.

---

## 🎯 Features Implemented

### 1. Search Posts
- Search by **title**
- Search by **content**
- Case-insensitive search
- Partial matching

### 2. Filter by Category
- Filter posts by category ID
- Single category filter

### 3. Filter by Tag
- Filter posts by tag ID
- Uses relationship filtering

### 4. Combine Filters
- Use search + category + tag together
- All filters work simultaneously

### 5. Pagination
- 10 posts per page
- Full pagination metadata
- Navigate between pages

### 6. Sorting
- Latest posts first
- Ordered by creation date

---

## 📖 API Usage

### Base URL
```
GET /api/posts
```

### Query Parameters

| Parameter | Type | Description | Example |
|-----------|------|-------------|---------|
| search | string | Search in title or content | `?search=laravel` |
| category | integer | Filter by category ID | `?category=1` |
| tag | integer | Filter by tag ID | `?tag=2` |
| page | integer | Page number for pagination | `?page=2` |

---

## 🚀 Usage Examples

### 1. Get All Posts (Paginated)
```
GET /api/posts
```

**Response:**
```json
{
    "success": true,
    "message": "Posts retrieved successfully",
    "data": [
        {
            "id": 1,
            "title": "Laravel Tutorial",
            "content": "Learn Laravel...",
            "user": {...},
            "category": {...},
            "tags": [...]
        }
    ],
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

### 2. Search Posts
```
GET /api/posts?search=laravel
```

Searches in:
- Post title
- Post content

**Example:**
```
GET /api/posts?search=tutorial
```

Returns all posts with "tutorial" in title or content.

---

### 3. Filter by Category
```
GET /api/posts?category=1
```

Returns only posts in category with ID 1.

**Example:**
```
GET /api/posts?category=2
```

---

### 4. Filter by Tag
```
GET /api/posts?tag=3
```

Returns only posts tagged with tag ID 3.

**Example:**
```
GET /api/posts?tag=1
```

---

### 5. Combine Search and Filters
```
GET /api/posts?search=react&category=1&tag=2
```

Returns posts that:
- Contain "react" in title or content
- AND belong to category 1
- AND have tag 2

**More Examples:**
```
# Search + Category
GET /api/posts?search=laravel&category=1

# Search + Tag
GET /api/posts?search=vue&tag=3

# Category + Tag
GET /api/posts?category=2&tag=1

# All filters
GET /api/posts?search=tutorial&category=1&tag=2
```

---

### 6. Pagination
```
GET /api/posts?page=2
```

Navigate to page 2.

**With filters:**
```
GET /api/posts?search=laravel&page=2
```

---

## 🧪 Testing in Postman

### Test 1: Get All Posts
1. Method: `GET`
2. URL: `http://localhost:8000/api/posts`
3. Click Send
4. Should return 10 posts (or less if fewer exist)

---

### Test 2: Search Posts
1. Method: `GET`
2. URL: `http://localhost:8000/api/posts?search=laravel`
3. Click Send
4. Should return only posts with "laravel" in title or content

---

### Test 3: Filter by Category
1. First, create a category (if not exists):
   - POST `http://localhost:8000/api/categories`
   - Body: `{"name": "Technology"}`
   - Note the category ID

2. Create posts with that category:
   - POST `http://localhost:8000/api/posts`
   - Body: `{"title": "Test", "content": "Content", "category_id": 1}`

3. Filter:
   - GET `http://localhost:8000/api/posts?category=1`
   - Should return only posts in that category

---

### Test 4: Filter by Tag
1. First, create a tag:
   - POST `http://localhost:8000/api/tags`
   - Body: `{"name": "Laravel"}`
   - Note the tag ID

2. Create posts with that tag:
   - POST `http://localhost:8000/api/posts`
   - Body: `{"title": "Test", "content": "Content", "tags": [1]}`

3. Filter:
   - GET `http://localhost:8000/api/posts?tag=1`
   - Should return only posts with that tag

---

### Test 5: Combined Filters
```
GET http://localhost:8000/api/posts?search=tutorial&category=1&tag=2
```

Should return posts matching all criteria.

---

### Test 6: Pagination
1. Create 15+ posts
2. GET `http://localhost:8000/api/posts`
   - Should return 10 posts
   - Check `pagination.total` = 15+
   - Check `pagination.last_page` = 2+

3. GET `http://localhost:8000/api/posts?page=2`
   - Should return remaining posts
   - Check `pagination.current_page` = 2

---

## 🧪 Testing in Browser

You can test directly in your browser:

```
http://localhost:8000/api/posts
http://localhost:8000/api/posts?search=laravel
http://localhost:8000/api/posts?category=1
http://localhost:8000/api/posts?tag=2
http://localhost:8000/api/posts?search=react&category=1&tag=2
http://localhost:8000/api/posts?page=2
```

---

## 💻 Code Implementation

### Controller Method (PostController.php)

```php
public function index(Request $request): JsonResponse
{
    $query = Post::query();

    // Search by title or content
    if ($request->has('search')) {
        $search = $request->input('search');
        $query->where(function ($q) use ($search) {
            $q->where('title', 'like', '%' . $search . '%')
              ->orWhere('content', 'like', '%' . $search . '%');
        });
    }

    // Filter by category
    if ($request->has('category')) {
        $query->where('category_id', $request->input('category'));
    }

    // Filter by tag
    if ($request->has('tag')) {
        $query->whereHas('tags', function ($q) use ($request) {
            $q->where('tags.id', $request->input('tag'));
        });
    }

    // Eager load relationships and paginate
    $posts = $query->with(['user:id,name,email', 'category', 'tags'])
                   ->latest()
                   ->paginate(10);

    return response()->json([
        'success' => true,
        'message' => 'Posts retrieved successfully',
        'data'    => $posts->items(),
        'pagination' => [
            'total'        => $posts->total(),
            'per_page'     => $posts->perPage(),
            'current_page' => $posts->currentPage(),
            'last_page'    => $posts->lastPage(),
            'from'         => $posts->firstItem(),
            'to'           => $posts->lastItem(),
        ],
    ]);
}
```

---

## 🔍 How It Works

### 1. Query Builder Pattern
```php
$query = Post::query();
```
Creates a query builder instance for dynamic filtering.

### 2. Search Implementation
```php
$query->where(function ($q) use ($search) {
    $q->where('title', 'like', '%' . $search . '%')
      ->orWhere('content', 'like', '%' . $search . '%');
});
```
- Uses `LIKE` for partial matching
- Searches both title and content
- Wrapped in closure for proper OR logic

### 3. Category Filter
```php
$query->where('category_id', $request->input('category'));
```
Simple where clause on foreign key.

### 4. Tag Filter
```php
$query->whereHas('tags', function ($q) use ($request) {
    $q->where('tags.id', $request->input('tag'));
});
```
- Uses `whereHas()` for relationship filtering
- Checks if post has specific tag

### 5. Eager Loading
```php
->with(['user:id,name,email', 'category', 'tags'])
```
- Prevents N+1 query problem
- Loads relationships efficiently
- Selects specific user fields

### 6. Sorting
```php
->latest()
```
Orders by `created_at` DESC (newest first).

### 7. Pagination
```php
->paginate(10)
```
- Returns 10 posts per page
- Includes pagination metadata
- Supports `?page=2` parameter

---

## 📊 Response Structure

```json
{
    "success": true,
    "message": "Posts retrieved successfully",
    "data": [
        {
            "id": 1,
            "title": "Post Title",
            "content": "Post content...",
            "category_id": 1,
            "user_id": 1,
            "image": "posts/image.jpg",
            "image_url": "http://localhost:8000/storage/posts/image.jpg",
            "created_at": "2026-05-07T...",
            "updated_at": "2026-05-07T...",
            "user": {
                "id": 1,
                "name": "John Doe",
                "email": "john@example.com"
            },
            "category": {
                "id": 1,
                "name": "Technology"
            },
            "tags": [
                {
                    "id": 1,
                    "name": "Laravel"
                }
            ]
        }
    ],
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

## ✅ Test Results

All 17 tests passing:

```
✓ anyone can get all posts
✓ authenticated user can create a post
✓ unauthenticated user cannot create a post
✓ owner can update their post
✓ non-owner cannot update a post
✓ owner can delete their post
✓ post can be created with category and tags
✓ authenticated user can create a post with image
✓ post image is deleted when post is deleted
✓ can search posts by title
✓ can search posts by content
✓ can filter posts by category
✓ can filter posts by tag
✓ can combine search and filters
✓ posts are paginated
✓ can navigate to second page
✓ posts are sorted by latest first
```

---

## 🎯 Use Cases

### Blog Homepage
```
GET /api/posts
```
Show latest 10 posts.

### Search Bar
```
GET /api/posts?search={user_input}
```
User types in search box.

### Category Page
```
GET /api/posts?category={category_id}
```
Show all posts in a category.

### Tag Page
```
GET /api/posts?tag={tag_id}
```
Show all posts with a tag.

### Advanced Search
```
GET /api/posts?search={query}&category={cat}&tag={tag}
```
Filter by multiple criteria.

---

## 🚀 Performance Tips

1. **Indexes**: Add database indexes on:
   - `posts.category_id`
   - `posts.created_at`
   - `post_tag.post_id`
   - `post_tag.tag_id`

2. **Eager Loading**: Always use `with()` to prevent N+1 queries

3. **Pagination**: Always paginate large result sets

4. **Caching**: Consider caching popular searches

---

## 🔧 Customization

### Change Posts Per Page
```php
->paginate(20) // 20 posts per page
```

### Change Sort Order
```php
->oldest() // Oldest first
->orderBy('title') // Alphabetical
```

### Add More Search Fields
```php
$q->where('title', 'like', '%' . $search . '%')
  ->orWhere('content', 'like', '%' . $search . '%')
  ->orWhere('excerpt', 'like', '%' . $search . '%');
```

---

## 📝 Summary

✅ Search by title and content  
✅ Filter by category  
✅ Filter by tag  
✅ Combine all filters  
✅ Pagination (10 per page)  
✅ Latest posts first  
✅ Eager loading relationships  
✅ Clean JSON responses  
✅ Full test coverage (17 tests)  

**The search and filtering system is production-ready!** 🎉
