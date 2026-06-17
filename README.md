# 🏪 Nusantara Connection (NUCO) - Integrated POS & Automated Transaction System

[![Laravel](https://img.shields.io/badge/laravel-%23FF2D20.svg?style=flat&logo=laravel&logoColor=white)](https://laravel.com/)
[![PHP](https://img.shields.io/badge/php-%23777BB4.svg?style=flat&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/mysql-4479A1.svg?style=flat&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Railway](https://img.shields.io/badge/Railway-0B0D0E?style=flat&logo=railway&logoColor=white)](https://railway.app/)

Nusantara Connection is a custom-built Point of Sales (POS) and operational management system engineered for a local culinary brand. Developed in a team of two, this system digitizes the brand's entire supply chain and order workflows to eliminate human error, optimize resource allocation, and provide real-time operational visibility.

---

## 📌 Project Context & Metadata

| Attribute | Details |
| :--- | :--- |
| 🎓 Institution | Universitas Ciputra Surabaya |
| 🚀 Academic Timeline | Semester 3 - Web Development Final Project |
| 📅 Development Period | November 2025 - January 2026 |
| 👥 Team Size | 2 Developers |
| 🌐 Deployment | Hosted on Railway |

---

## 🛠️ Technical Features & Logic

### 🔐 Architecture & Security Engine
- MVC Architecture: Implemented a clean Model-View-Controller pattern utilizing Laravel to ensure a highly modular, readable, and scalable codebase.
- 6-Tier Role-Based Access Control (RBAC): Engineered a secure multi-user gatekeeping system with granular permissions explicitly tailored for 6 distinct roles: Owner, Waiter, Chef, Cashier, Reviewer, and Guest.

### 🔄 Automated Supply Chain & Workflow
- Automated Inventory Control: Built a real-time stock deduction system governed by specific recipe ingredient matrices, triggered automatically the exact moment an order status updates to completed.
- Full-Cycle Operational Workflow: Seamlessly synchronized digital customer ordering, dynamic table mapping coordinates, a live Kitchen Display System (KDS) for back-of-house staff, and front-of-house payment checkouts.

### 💳 Payment Gateways & Business Intelligence
- Midtrans API Integration: Integrated Midtrans payment gateway to handle secure end-to-end digital transactions, complete with automated webhook listeners that update database order statuses and stock values asynchronously.
- Financial Analytics & Audit Logs: Developed a dedicated executive dashboard for the Owner, featuring append-only audit logging to guarantee financial transparency, prevent data tampering, and protect database integrity.

---

## 💻 Tech Stack

- Framework: Laravel (MVC)
- Language: PHP
- Database Engine: MySQL
- Payment Gateway API: Midtrans API
- Deployment Platform: Railway Cloud Hosting
