# PHP REST API

A clean, modern, and lightweight RESTful API built in PHP 8.x (fully compatible with PHP 8.4 and PHP 8.5+). It performs secure CRUD operations on a `users` table using PDO prepared statements.

---

## 🚀 Features

- **PHP 8.5+ Ready:** Fully compatible with modern PHP versions (uses strict types, modern type hints, avoids deprecated functions).
- **Environment Configurations:** Database settings are kept safe using `.env` variables.
- **Secure by Design:** Protects against SQL Injection through PDO prepared statements, and sanitizes input vectors.
- **CORS Support:** Integrated Cross-Origin Resource Sharing headers including `OPTIONS` preflight request support.
- **Zero Dependencies:** Runs on pure, native PHP without requiring third-party library installations (e.g. Composer).

---

## 🛠️ Requirements

- **PHP** >= 8.1 (tested and optimized for 8.4/8.5+)
- **MySQL** / **MariaDB**
- Apache or Nginx with URL rewriting supported (if required)

---

## 📦 Installation & Setup

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/tolgahanacar/php-rest-api.git
   cd php-rest-api
   ```

2. **Configure Environment Variables:**
   Copy the template environment file and fill in your database credentials:
   ```bash
   cp .env.example .env
   ```
   Edit `.env`:
   ```env
   DB_HOST=localhost
   DB_PORT=3306
   DB_NAME=rest-api
   DB_USER=your_db_user
   DB_PASS=your_db_password
   DB_CHARSET=utf8mb4
   ```

3. **Database Schema Setup:**
   Create a database named `rest-api` and run the following SQL schema to create the `users` table:

   ```sql
   CREATE TABLE IF NOT EXISTS `users` (
     `id` int(11) NOT NULL AUTO_INCREMENT,
     `username` varchar(60) NOT NULL,
     `first_name` varchar(50) NOT NULL,
     `last_name` varchar(50) NOT NULL,
     `email` varchar(60) NOT NULL,
     `updateDate` timestamp NOT NULL DEFAULT current_timestamp() ON UPDATE current_timestamp(),
     `createDate` timestamp NOT NULL DEFAULT current_timestamp(),
     PRIMARY KEY (`id`)
   ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
   ```

---

## 📡 API Endpoints

All requests should be sent to `index.php`. The API handles routing automatically based on the HTTP method.

### 1. Get All Users
- **Method:** `GET`
- **URL:** `http://localhost/rest-api/index.php`
- **Response (200 OK):**
  ```json
  {
    "error": false,
    "User information": [
      {
        "id": 1,
        "username": "johndoe",
        "first_name": "John",
        "last_name": "Doe",
        "email": "john.doe@example.com",
        "updateDate": "2026-07-06 12:00:00",
        "createDate": "2026-07-06 12:00:00"
      }
    ],
    "200": "OK"
  }
  ```

### 2. Get Single User
- **Method:** `GET`
- **URL:** `http://localhost/rest-api/index.php?id={user_id}`
- **Response (200 OK):**
  ```json
  {
    "error": false,
    "User information": {
      "id": 1,
      "username": "johndoe",
      "first_name": "John",
      "last_name": "Doe",
      "email": "john.doe@example.com"
    },
    "200": "OK"
  }
  ```
- **Response (404 Not Found):**
  ```json
  {
    "error": true,
    "errorMessage": "No value found for your request!",
    "404": "Not Found"
  }
  ```

### 3. Create User
- **Method:** `POST`
- **URL:** `http://localhost/rest-api/index.php`
- **Content-Type:** `application/x-www-form-urlencoded`
- **Body parameters:**
  - `username` (string, required)
  - `first_name` (string, required)
  - `last_name` (string, required)
  - `email` (string, required, valid email format)
- **Response (201 Created):**
  ```json
  {
    "error": false,
    "send_data": "Data sending is successful",
    "username": "johndoe",
    "first_name": "John",
    "last_name": "Doe",
    "email": "john.doe@example.com",
    "201": "Created"
  }
  ```

### 4. Update User
- **Method:** `PUT`
- **URL:** `http://localhost/rest-api/index.php`
- **Content-Type:** `application/json`
- **Body JSON:**
  ```json
  {
    "id": 1,
    "username": "johndoe_updated",
    "first_name": "John",
    "last_name": "Doe",
    "email": "john.doe@example.com"
  }
  ```
- **Response (200 OK):**
  ```json
  {
    "error": false,
    "message": "Update successful",
    "affectedId": 1,
    "200": "OK"
  }
  ```

### 5. Delete User
- **Method:** `DELETE`
- **URL:** `http://localhost/rest-api/index.php?id={user_id}`
- **Response (200 OK):**
  ```json
  {
    "error": false,
    "message": "Deletion successful",
    "affectedId": 1,
    "200": "OK"
  }
  ```

---

## 🧪 Testing with REST Client

You can use the built-in [request.rest](file:///c:/Users/tolga.acar/Documents/GitHub/php-rest-api/request.rest) file with the **REST Client** extension for VS Code to perform direct test queries.

---

## 📄 License

This project is open source and available under the [MIT License](LICENSE).
