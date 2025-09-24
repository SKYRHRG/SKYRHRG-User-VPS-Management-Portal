SKYRHRG User Management Portal
A robust, role-based user management portal with a hierarchical permission system, built with PHP and a modern frontend stack.

This project provides a secure and scalable foundation for managing users with different levels of authority. It is designed with a clean separation of roles, where higher-level users can create and manage users below them in the hierarchy.

Key Features ✨
Role-Based Access Control (RBAC): Four distinct user roles (Admin, Super Seller, Seller, User), each with a dedicated panel and specific permissions.

Hierarchical System: Users can only create and manage other users who are at a lower level in the hierarchy, ensuring a clear chain of command.

Full Admin Control: The Admin role has complete CRUD (Create, Read, Update, Delete) capabilities over all users in the system.

Scoped Management: Super Sellers and Sellers can only create and view users they are responsible for.

Secure Authentication: Features a secure login system with password hashing (PASSWORD_DEFAULT), session protection, and CSRF tokens on all forms.

Modern UI/UX: Built with Bootstrap 5 and the professional AdminLTE 3 template for a responsive and intuitive user experience.

Dynamic Data Tables: User lists are enhanced with DataTables.js, providing instant search, sorting, and pagination.

Interactive Alerts: User-friendly and non-disruptive notifications and confirmation dialogs powered by SweetAlert2.

Role Hierarchy Explained 👑
The core of this portal is its strict hierarchical structure.

👑 Admin

Has full control over the entire system.

Can create, view, edit, and delete any user (Super Sellers, Sellers, and Users).

🛡️ Super Seller

Can create and view Sellers and Users.

Cannot edit or delete any users.

Cannot create other Super Sellers or Admins.

💼 Seller

Can create and view Users only.

Cannot edit or delete any users.

Cannot create any other roles.

👤 User

The base-level account.

Has access to a personal dashboard and profile.

Cannot create or manage any other users.

Technology Stack 💻
Backend
PHP 8+ (with MySQLi Extension)

MySQL Database

Frontend
HTML5

CSS3 (Custom + Frameworks)

JavaScript (ES6)

Frameworks & Libraries
Bootstrap 5: For responsive design and UI components.

AdminLTE 3: For the overall dashboard template and structure.

jQuery: As a dependency for Bootstrap and AdminLTE.

DataTables.js: For advanced and interactive HTML tables.

SweetAlert2: For beautiful and responsive alerts.

Project Structure
highdatacenter/
├── admin/
│   ├── create_user.php
│   ├── delete_user.php
│   ├── edit_user.php
│   ├── index.php
│   ├── manage_customers.php
│   ├── manage_deposits.php
│   ├── manage_sellers.php
│   ├── manage_super_sellers.php
│   ├── manage_users.php
│   └── manage_wallets.php
│   └── includes/
│       ├── footer.php
│       ├── header.php
│       ├── navbar.php
│       └── sidebar.php
├── ajax/
│   ├── get_transactions.php
│   ├── get_user_dashboard_details.php
│   └── process_deposit.php
├── assets/
│   ├── css/
│   │   └── style.css
│   └── js/
│       └── script.js
├── includes/
│   ├── db.php
│   ├── functions.php
│   └── session.php
├── seller/
│   ├── create_user.php
│   ├── index.php
│   ├── manage_users.php
│   ├── transfer_balance.php
│   └── wallet.php
│   └── includes/
│       ├── footer.php
│       ├── header.php
│       ├── navbar.php
│       └── sidebar.php
├── super_seller/
│   ├── create_seller.php
│   ├── create_user.php
│   ├── index.php
│   ├── manage_sellers.php
│   ├── manage_users.php
│   ├── transfer_balance.php
│   └── wallet.php
│   └── includes/
│       ├── footer.php
│       ├── header.php
│       ├── navbar.php
│       └── sidebar.php
├── user/
│   ├── add_fund.php
│   ├── index.php
│   ├── profile.php
│   └── wallet.php
│   └── includes/
│       ├── footer.php
│       ├── header.php
│       ├── navbar.php
│       └── sidebar.php
├── index.php
├── login.php
└── logout.php
Getting Started 🚀
Follow these steps to set up the project on your local machine.

Prerequisites
A local server environment like XAMPP, WAMP, or MAMP.

PHP 8.0 or newer.

A MySQL database server.

Installation
Clone the repository:

Bash

git clone <your-repository-url>
Create the Database:

Open your database management tool (e.g., phpMyAdmin).

Create a new database named skyrhrg_ump.

Import the SQL file:

Select the skyrhrg_ump database.

Go to the "Import" tab and import the provided .sql file which contains the table structure and demo data.

Configure Database Connection:

Open the file includes/db.php.

Update the DB_USER and DB_PASS constants with your database username and password.

PHP

define('DB_USER', 'root'); // Your DB username
define('DB_PASS', '');     // Your DB password
Run the project:

Place the project folder in your server's web root (e.g., htdocs for XAMPP).

Open your web browser and navigate to http://localhost/SKYRHRG_User_Management_Portal/ (or your project's folder name).

Usage
After installation, you can log in with the default admin credentials to start managing the system.

URL: http://localhost/your_project_folder/login.php

Admin Username: admin

Admin Password: admin123


You can also log in with the other demo accounts to explore the permissions of each role. The password for all demo accounts is demo123.
# VPS Control Panel – Virtualizor API Integration

A PHP-based control panel for managing VPS servers via the **Virtualizor Enduser API**.  
From a single dashboard you can list, start, stop, reboot, reinstall OS, and monitor VPS status.

---

## 📂 Project Structure

```
includes/
  VirtualizorAPI.php          # PHP wrapper for Virtualizor Enduser API
  vps_providers_config.php    # Provider API keys, URLs, credentials
manage_vps_orders.php         # Listing VPS orders / customers
vps_control.php               # Main UI to control VPS (start/stop/reboot/OS reinstall)
assets/css/…                  # CSS, icons, animations
```

---

## ✨ Features

- List VPS and show status (IP, hostname, OS, RAM, bandwidth).
- Start / Stop / Reboot VPS.
- Reinstall OS from available templates.
- Colorful, animated DataTable UI with action buttons.
- Logging of all API calls and errors to `virtualizor_api_errors.log`.

---

## 🔑 How It Works

1. **Configuration**  
   All API keys, endpoint URLs, ports, and credentials live in  
   `includes/vps_providers_config.php`:

   ```php
   return [
       'provider_name' => [
           'api_url'  => 'https://cp.example.com:4083/index.php',
           'api_key'  => 'YOUR_API_KEY',
           'api_pass' => 'YOUR_API_PASS',
           'timeout'  => 60, // request timeout in seconds
       ],
       // more providers…
   ];
   ```

2. **API Wrapper** (`VirtualizorAPI.php`)  
   Provides methods like:

   ```php
   $vapi = new VirtualizorAPI($api_url, $api_key, $api_pass);
   $vapi->listVS($vpsid);             // get VPS info
   $vapi->startVPS($vpsid);           // start
   $vapi->stopVPS($vpsid);            // stop
   $vapi->rebootVPS($vpsid);          // reboot
   $vapi->reinstallOS($vpsid, $osid); // reinstall OS
   $vapi->getOsTemplates();           // list OS templates
   ```

   - Handles **cURL**, SSL, timeouts, error logging.
   - If Virtualizor returns HTML instead of JSON (long-running commands), wrapper marks it as **queued** so the UI can poll for status.

3. **UI** (`vps_control.php`)  
   - Displays VPS info in a colorful DataTable.
   - Buttons: **Start / Stop / Reboot / Reinstall OS**.
   - Calls AJAX to `vps_control.php` which uses `VirtualizorAPI.php`.
   - Shows spinner/toast while action is queued; auto-refreshes status.

---

## 🖥️ Installation & Setup

### Requirements
- PHP 7.x or 8.x with **cURL** enabled.
- A Virtualizor server with Enduser API enabled.

### Steps
1. Clone/download the repository.
2. Configure your providers in `includes/vps_providers_config.php`:

   ```php
   return [
     'Hyper Sonic Server' => [
        'api_url'  => 'https://cp.securednscloud.com:4083/index.php',
        'api_key'  => 'AA0LQGAPFWOSMEIK',
        'api_pass' => 'D1m5ADvbRxMXQc3XUATJejCVi6KYNRQK',
        'timeout'  => 60
     ],
   ];
   ```

3. Include the wrapper in your scripts:

   ```php
   require_once __DIR__.'/includes/VirtualizorAPI.php';
   require_once __DIR__.'/includes/vps_providers_config.php';

   $cfg = include 'includes/vps_providers_config.php';
   $provider = $cfg['Hyper Sonic Server'];

   $api = new VirtualizorAPI(
       $provider['api_url'],
       $provider['api_key'],
       $provider['api_pass'],
       $provider['timeout']
   );
   ```

4. Make API calls:

   ```php
   $info = $api->listVS($vpsid);
   if ($info['status'] === 1) {
       echo "VPS is online!";
   }

   $api->startVPS($vpsid);
   ```

---

## 📝 Parameters & Responses

- **listVS($vpsid)** → returns array with keys: `status`, `ip`, `hostname`, `os`, `ram`, `bandwidth`, etc.
- **startVPS / stopVPS / rebootVPS** → returns:

  ```php
  ['status' => 'done', 'message' => 'The VPS was started successfully']
  // or
  ['status' => 'queued', 'message' => 'Command sent, waiting for status update…']
  ```

- **reinstallOS($vpsid, $osid)**  
  `$osid` comes from `$api->getOsTemplates()` list.  
  Returns either **queued** or **done**.

---

## ⚠️ Error Handling

- All cURL and API errors logged to `virtualizor_api_errors.log` with JSON context.
- **Timeouts**: increase `timeout` in config.
- **SSL verify errors**: set `$verify_ssl=false` in `VirtualizorAPI.php` if needed (not recommended for production).
- **HTML instead of JSON**: Virtualizor returned progress page; wrapper marks as **queued**.

---

## 🚀 Roadmap / Future Enhancements

- Auto polling until start/stop/reboot finishes.
- Bulk actions for multiple VPS.
- More detailed resource usage graphs.

---

## 📚 References

- [Virtualizor API Docs](https://www.virtualizor.com/docs/enduser-api/)
- This repository’s source code.
