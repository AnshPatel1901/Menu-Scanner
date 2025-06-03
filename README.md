# 🍽️ DigiDine – QR Menu & Restaurant Manager

A full-stack restaurant system that enables customers to **scan a QR code** to view the digital menu, place orders, and track them — while admins, managers, and chefs handle the backend process.

---

## 📌 Features

### Customer Side
- 📲 **QR Menu Access** – Scan a QR code to open the digital menu.
- 🍽️ **Live Menu Display** – View menu items categorized and styled clearly.
- 🛒 **Cart System** – Add, update, or remove items before placing an order.
- 🧾 **Order Placement & Tracking** – Submit orders and view their status.

### Admin/Staff Side
- 🧑‍💼 **Admin Panel** – Manage users, assign roles, and oversee the system.
- 📋 **Manager Dashboard** – Control menu items, availability, and price.
- 👨‍🍳 **Chef Dashboard** – View placed orders and update status as they are prepared.
- 📦 **Order Management** – Full visibility of order flow from placement to completion.

---

## 🧰 Tech Stack

| Layer      | Technology             |
|------------|------------------------|
| Frontend   | HTML, CSS, JavaScript  |
| Backend    | PHP (Core PHP)         |
| Database   | MySQL                  |
| Tools      | Git, GitHub, VS Code   |

---

## 📁 Folder Structure

```

/digidine/
├── assets/
│   ├── css/
│   │   └── style.css
│   └── images/
│
├── auth/
│   ├── login.php
│   ├── register.php
│   └── logout.php
│
├── includes/
│   └── db.php
│
├── pages/
│   ├── admin/
│   │   └── dashboard.php
│   ├── manager/
│   │   └── dashboard.php
│   ├── chef/
│   │   └── dashboard.php
│   └── customer/
│       ├── dashboard.php
│       ├── menu.php
│       ├── cart.php
│       ├── place_order.php
│       └── orders.php
│
├── functions/
│   ├── manage_menu.php
│   └── manage_users.php
│
├── database/
│   └── digidine_schema.sql   ← your exported database file
│
├── dashboard.php             ← main redirector after login
├── index.php                 ← QR landing/homepage
├── README.md                 ← project documentation

````

---

## 🚀 Getting Started

### 1. Clone the Project

```bash
git clone https://github.com/your-username/digidine.git
cd digidine
````

### 2. Setup the Database

- Open your MySQL client (like phpMyAdmin or MySQL CLI).
- Import the file located at: `database/digidine_schema.sql`
- Make sure your `includes/db.php` has the correct credentials:

```php
$conn = new mysqli("localhost", "root", "", "digidine");


### 3. Run Locally

Use XAMPP/WAMP and navigate to:

```
http://localhost/DigiDine/
```

Scan a QR code linked to `index.php` to simulate the customer experience.

---

## 🔒 Roles & Access

| Role     | Access Level                     |
| -------- | -------------------------------- |
| Admin    | Full access, user & menu control |
| Manager  | Menu & orders overview           |
| Chef     | Incoming order queue             |
| Customer | View menu, place & track order   |

---

## 📄 License

MIT License — Free to use, modify, and distribute.

---

## 🙏 Acknowledgments

Thanks to the PHP and MySQL open source community and everyone contributing to restaurant tech innovation!

```

---