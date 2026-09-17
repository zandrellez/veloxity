
## **Feature 3: Just-In-Time (JIT) Google OAuth 2.0 Social Login**

### **1. Feature Overview**

-   **Name & ID:** Feature 03 - JIT Google OAuth 2.0 Social Login
    
-   **Objective:** Reduce user onboarding friction by allowing secure, passwordless authentication and automatic account provisioning using Google credentials.
    
-   **Actor(s):** Customers.
    

### **2. User Journey & Flow**

-   **Step-by-Step Interaction:**
    
    1.  The user clicks the **Google** sign-in button on the authentication portal (`google_login.php`).
        
    2.  They are redirected to Google's official consent and authentication screen.
        
    3.  Upon approval, Google redirects back to `google_callback.php` with an authorization code.
        
    4.  The backend exchanges the code via cURL for an access token, fetches the user's Google profile, and logs them straight into their dashboard.
        
-   **Redirection & States:** New users are automatically registered and routed to `customer/dashboard.php` instantly.
    

### **3. Technical Architecture & File Map**

-   **Involved Files:** `actions/google_login.php`, `actions/google_callback.php`, `includes/supabase.php`.
    
-   **Dependencies:** Google OAuth 2.0 REST endpoints, PHP cURL extension.
    

### **4. Database & Data Schema Impact**

-   **Tables Modified:** `users` and `saved_passengers`.
    
-   **State Changes:**
    
    -   The `password` column is allowed to be `NULL` to support social accounts.
        
    -   New Google users are automatically set as verified (`is_verified = 1`).
        
    -   A default row in `saved_passengers` is auto-seeded for the newly provisioned user.
        
    -   Existing unverified standard accounts are automatically updated to verified status upon a successful Google login with the same email.
        

### **5. Security & Edge Case Guardrails**

-   **Token Exchange Security:** Authorization codes are securely exchanged over server-to-server cURL requests, keeping client secrets hidden from the browser.
    
-   **State Interoperability:** Handles duplicate email overlaps cleanly by bridging standard unverified sign-ups with verified social authentication flows.