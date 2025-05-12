# Movers Admin Profile Management System

## 📌 Project Overview

The **Movers Admin Profile Management System** is a module within a broader ride-booking or logistics web application tailored for managing administrative user accounts. This component ensures that only authenticated administrators can securely access their profile data, update passwords, and manage system responsibilities through a clean and responsive interface.

This system demonstrates secure session-based authentication, encrypted password handling, and dynamic database integration—forming a fundamental part of any secure admin dashboard or user management portal.

---

## 🎯 Objectives

- To **protect access** to admin-only sections using session control and role-based authorization.
- To **display profile details** (name and email) of the logged-in admin user.
- To allow the admin to **update their password** securely via form submission and hashing mechanisms.
- To enhance user experience using a clean, modern front-end layout and responsive design principles.

---

## 🧠 Core Functionalities

1. **Session and Role Validation**
   - Ensures that only authenticated users with an `admin` role can access the profile page.
   - Redirects unauthorized users to the login page.

2. **Profile Display**
   - Retrieves and displays admin details from the `users` table in the database.
   - Uses secure queries with prepared statements to avoid SQL injection.

3. **Password Update**
   - Admins can update their password by providing and confirming the new password.
   - Passwords are hashed using `password_hash()` before being stored in the database for security.

4. **User Interface**
   - Implements a responsive layout using Bootstrap 5.
   - Utilizes a professional theme with a custom background image and Google Fonts for branding and aesthetic appeal.

---

## 🛠 Technologies Used

- **Backend**: PHP
- **Database**: MySQL (with prepared statements)
- **Frontend**: HTML5, CSS3, Bootstrap 5
- **Security**: PHP Sessions, Password Hashing (`password_hash`)
- **Assets**: Custom background images, Google Fonts (Poppins)

---

## 🔐 Security Measures

- Passwords are never stored in plain text.
- Input is sanitized via HTML escaping and prepared SQL statements.
- Only users with an `admin` role can access the profile management functionality.
- User sessions are validated on each page load.

---

## 💼 Use Case

This profile management system is ideal for:

- Admin control panels in logistics or ride-booking platforms.
- Role-based portals where administrators manage bookings, user requests, or system settings.
- Any PHP-based system needing secure profile and password management.

---

## 📁 How It Fits into the Bigger System

This module is assumed to be a part of a larger **Movers System**, which could include:

- Booking rides or logistics services
- Tracking rides
- Managing drivers and customers
- Handling payments and ride confirmations

The **admin profile system** ensures that the platform remains secure by giving only authorized personnel access to sensitive operations and personal data updates.

---

## 🚀 Future Enhancements

- Add profile picture update functionality.
- Implement two-factor authentication (2FA).
- Log password update attempts and store timestamp metadata.
- Integrate with a full admin dashboard for deeper analytics and control.

---

## 🤝 Author

**Vincent Kimani Njoroge**  
Admin System Developer | PHP Enthusiast | Web Systems Designer

---

