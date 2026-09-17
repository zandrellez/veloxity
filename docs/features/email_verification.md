
## **Feature 2: Strict Email Verification Pipeline**

### **1. Feature Overview**

-   **Name & ID:** Feature 02 - Strict Email Verification Pipeline
    
-   **Objective:** Ensure account authenticity and valid communication channels by enforcing mandatory email verification before system access is granted.
    
-   **Actor(s):** New Customers.
    

### **2. User Journey & Flow**

-   **Step-by-Step Interaction:**
    
    1.  Upon completing registration, an unverified account (`is_verified = 0`) is created in the database.
        
    2.  A cryptographically secure verification token with a 24-hour expiration window is generated.
        
    3.  PHPMailer dispatches an HTML verification email via live Gmail SMTP.
        
    4.  Clicking the link routes the user to `verify.php`, which validates the token, flips `is_verified` to `1`, clears the token fields, and automatically logs them in.
        
-   **Redirection & States:** If an unverified user attempts to log in normally, they are blocked and presented with a direct action link to resend the verification email via `resend_verification.php`.
    

### **3. Technical Architecture & File Map**

-   **Involved Files:** `actions/auth_process.php`, `actions/verify.php`, `actions/resend_verification.php`, `includes/PHPMailer/*`.
    
-   **Dependencies:** PHPMailer library, Gmail SMTP.
    

### **4. Database & Data Schema Impact**

-   **Tables Modified:** `users` table columns (`verification_token`, `token_expires_at`, `is_verified`).
    
-   **State Changes:** Token fields are populated on signup and nulled out upon successful verification.
    

### **5. Security & Edge Case Guardrails**

-   **Dynamic Path Resolution:** Uses `dirname($_SERVER['SCRIPT_NAME'])` to dynamically build verification URLs, supporting both local XAMPP subfolders (`/veloxity/verify.php`) and root cloud deployments (Render) without breaking.
    
-   **User Enumeration Prevention:** The resend script outputs generic security messages to prevent attackers from discovering valid system emails.