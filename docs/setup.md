# Veloxity - Local Development Setup Guide

Follow this step-by-step installation manual to set up Veloxity locally on your development machine using XAMPP and Supabase PostgreSQL.

---

## 📋 Prerequisites
* **XAMPP Control Panel** (Featuring PHP 8.2+ and Apache)
* **A Supabase Account** (For your cloud PostgreSQL database instance)
* **A Google Cloud Console Project** (Optional, for Google OAuth login testing)
* **A Gmail Account with App Passwords Enabled** (For PHPMailer SMTP email verification testing)

---

## ⚙️ Step-by-Step Installation

### **1. Clone or Place Project Files**
Move your `veloxity` project folder directly into your XAMPP local server root directory:
```text
C:\xampp\htdocs\veloxity\
```

### **2. Configure Environment Variables**
Create a `.env` file in the root directory of your project (`veloxity/.env`) by referencing the provided template in `docs/.env.example`. Populate your local keys.

### **3. Set Up the Database**
1.  Log in to your **Supabase Dashboard**.
2.  Navigate to the **SQL Editor** tab.
3.  Copy and execute the complete schema script from `includes/schema.sql` to build your tables, custom enums, and foreign key relationships.
### **4. Start Local Apache Server**
1.  Open the **XAMPP Control Panel**. 
2.  Start the **Apache** module.
3.  Open your browser and navigate to your local entry point:    
``http://localhost/veloxity/``