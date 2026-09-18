# Veloxity - Multi-Operator Transit & Cargo Portal

Veloxity is a high-performance, secure operational architecture designed to connect transit hubs, streamline passenger journey scheduling, and deliver end-to-end cargo transparency. It features multi-operator management, robust security enforcement, and real-time tracking logic tailored for modern transport logistics.

---

## 🚀 Core Features & Architecture

### **1. Advanced Authentication & Security**
* **Multi-Role Workspace:** Unified sign-in portal that dynamically routes users based on permissions (`customer`, `operator_admin`, `super_admin`).
* **Strict Email Verification:** Token-based verification utilizing cryptographic keys (`bin2hex`) with a strict 24-hour expiration guardrail.
* **Google and Facebook Login:** Passwordless social login via direct REST endpoints and cURL, automatically provisioning accounts and seeding default passenger profiles.
* **Backend Security:** Password strength enforcement, case-insensitive email matching, and robust SQL injection protection via PHP PDO.

### **2. Passenger Profile & Management**
* **Saved Passengers:** Automated database seeding of primary account holders into secondary passenger profile lists for lightning-fast multi-operator booking.
* **Discount Management:** Support for specialized passenger classifications including Regular, Senior, PWD, and Student discount tracking.

### **3. Terminal-to-Terminal Transit Booking**
* **Fleet & Route Mapping:** Relational database architecture connecting origin/destination terminals, distance metrics, base fares, and operator bus/ferry capacities.
* **Trip Schedules:** Live operational status flows covering Scheduled, Boarding, Departed, Completed, and Cancelled trips.
* **Reservation Engine:** Secure booking references with dynamic seat selection and payment status tracking.

### **4. Cargo & Parcel Shipment Tracking**
* **Waybill Management:** Comprehensive cargo tracking capturing sender/recipient credentials, route logistics, item descriptions, weight calculations, and shipping fees.
* **Milestone Tracking:** Real-time lifecycle logging from initial acceptance to final destination pickup and claim verification.

---

## 🛠️ Technology Stack
* **Backend:** Vanilla PHP 8.2+ (No Composer overhead for core routing)
* **Database & Hosting:** PostgreSQL (Managed via Supabase Cloud)
* **Email Dispatch:** PHPMailer with live SMTP integration (Gmail TLS)
* **Frontend Design:** Tailwind CSS combined with custom high-energy dark slate and blaze orange UI styling (`globals.css`)
* **Deployment:** Containerized Apache & PHP environment optimized for cloud deployment via Render

---

## 📁 Project Directory Structure
```text
veloxity/
├── assets/             # Global CSS design systems and scripts
├── actions/            # Backend processing scripts
├── includes/           # Database connections (Supabase PDO) & PHPMailer library
├── customer/           # Customer dashboard and workspace views
├── operator/           # Operator and fleet management dashboards
├── admin/              # Super-admin control panels
├── tests/              # Automated integration and security test suites
└── docs/               # Technical documentation and setup guides
