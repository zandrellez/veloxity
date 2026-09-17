
# Veloxity Feature Documentation: Authentication & Security Subsystems

## **Feature 1: Secure Authentication & Sliding Portal**

### **1. Feature Overview**

-   **Name & ID:** Feature 01 - Secure Authentication & Sliding Portal
    
-   **Objective:** Provide a unified, interactive portal for users to switch seamlessly between signing in and registering, enforcing strict client-side constraints and role-based access control.
    
-   **Actor(s):** Customers, Operators, Administrators.
    

### **2. User Journey & Flow**

-   **Step-by-Step Interaction:**
    
    1.  The user arrives at `auth.php`. By default, the Sign-In view is displayed.
        
    2.  Clicking the **"Sign Up"** panel trigger animates the container into the registration view.
        
    3.  The user inputs their credentials (including a dynamic Philippine mobile prefix `+63`).
        
    4.  Real-time client-side validation checks password strength (via a 5-segment meter) and confirmation match before form submission.
        
-   **Redirection & States:** On successful sign-in, the system evaluates the user's role and routes them to `customer/dashboard.php`, `operator/dashboard.php`, or `admin/dashboard.php`.
    

### **3. Technical Architecture & File Map**

-   **Involved Files:** `auth.php`, `assets/css/globals.css`, `assets/js/auth.js`, `actions/auth_process.php`.
    
-   **Dependencies:** Tailwind CSS, FontAwesome icons.
    

### **4. Database & Data Schema Impact**

-   **Tables Modified:** Reads and writes to the `users` table.
    
-   **State Changes:** Validates stored password hashes against user input using PHP's native `password_verify()`.
    

### **5. Security & Edge Case Guardrails**

-   **Validation Rules:** Backend enforcement requires passwords to be at least 8 characters long with a mix of letters and numbers.
    
-   **Security Measures:** Implements session persistence and role-based routing checks to prevent unauthorized workspace access.