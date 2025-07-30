# Blog API (Laravel)

A simple RESTful API for a blog system built using Laravel.

## 📦 Features

- User authentication with Laravel Sanctum
- Create, update, delete blog posts
- Upload and update post images
- Comment system
- Users can post and comment
- FormRequest validation
- Authorization using Laravel Policies

## 🧱 Technologies

- Laravel 12
- PHP 8.2
- MySQL
- Sanctum (for API auth)
- Postman or Hoppscotch for testing

## ⚙️ Installation

```bash
git clone https://github.com/Abdo0-bit/Blog-API-Laravel.git
cd Blog-API-Laravel
composer install
cp .env.example .env
php artisan key:generate
php artisan migrate
php artisan serve
```

## 🔐 Authentication

This project uses **Laravel Sanctum** for API token authentication.  
After login/register, use the token in the `Authorization` header:

```
Authorization: Bearer your_token_here
```

## 🔄 API Endpoints (Examples)

| Method | Endpoint                 | Description         |
| ------ | ------------------------ | ------------------- |
| GET    | /api/posts               | Get all posts       |
| GET    | /api/posts/{id}          | Show single post    |
| POST   | /api/posts               | Create new post     |
| PUT    | /api/posts/{id}          | Update post         |
| DELETE | /api/posts/{id}          | Delete post         |
| POST   | /api/posts/{id}/like     | Like a post         |
| POST   | /api/posts/{id}/comments | Add comment to post |
| PUT    | /api/posts/{id}/image    | Update post image   |
| DELETE | /api/posts/{id}/image    | Delete post image   |

## 🧪 Testing

You can test the API using **Postman** or **Hoppscotch**.  
Make sure to set the token in the `Authorization` header.

---

## 🙌 Author

Developed by [Abdo0-bit](https://github.com/Abdo0-bit)
