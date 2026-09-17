-- 1. USERS TABLE (System accounts: Customers, Operators, Admins)
CREATE TABLE users (
user_id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100) NOT NULL,
email VARCHAR(100) UNIQUE NOT NULL,
password VARCHAR(255) NOT NULL,
contact VARCHAR(20) NOT NULL,
role ENUM('customer', 'operator_admin', 'super_admin') DEFAULT 'customer',
verification_token VARCHAR(255) NULL,
token_expires_at DATETIME NULL,
is_verified TINYINT(1) DEFAULT 0,
otp_code VARCHAR(6) NULL,
otp_expires_at DATETIME NULL,
google_id VARCHAR(255) NULL,
facebook_id VARCHAR(255) NULL,
auth_provider ENUM('local', 'google', 'facebook') DEFAULT 'local',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
);

-- 2. SAVED PASSENGERS / FAMILY PROFILES (For fast booking)
CREATE TABLE saved_passengers (
passenger_id INT AUTO_INCREMENT PRIMARY KEY,
user_id INT NOT NULL, -- The account holder who saved this profile
name VARCHAR(100) NOT NULL,
age INT NOT NULL,
sex VARCHAR(10),
contact VARCHAR(20),
passenger_type ENUM('Regular', 'Senior', 'PWD', 'Student') DEFAULT 'Regular',
discount_id VARCHAR(50) NULL,
FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- 3. TERMINALS TABLE (Origins and Destinations)
CREATE TABLE terminals (
terminal_id INT AUTO_INCREMENT PRIMARY KEY,
terminal_name VARCHAR(150) NOT NULL,
city VARCHAR(100) NOT NULL
);

-- 4. OPERATORS TABLE (Bus / Ferry lines)
CREATE TABLE operators (
operator_id INT AUTO_INCREMENT PRIMARY KEY,
operator_name VARCHAR(150) NOT NULL,
contact_email VARCHAR(100),
contact_phone VARCHAR(20)
);

-- 5. BUS / FLEET TABLE
CREATE TABLE buses (
bus_id INT AUTO_INCREMENT PRIMARY KEY,
operator_id INT NOT NULL,
plate_number VARCHAR(20) UNIQUE NOT NULL,
seat_capacity INT NOT NULL,
FOREIGN KEY (operator_id) REFERENCES operators(operator_id) ON DELETE CASCADE
);

-- 6. ROUTES TABLE (Connecting origin and destination terminals)
CREATE TABLE routes (
route_id INT AUTO_INCREMENT PRIMARY KEY,
origin_id INT NOT NULL,
destination_id INT NOT NULL,
distance_km DECIMAL(6,2) NOT NULL,
base_fare DECIMAL(10,2) NOT NULL,
FOREIGN KEY (origin_id) REFERENCES terminals(terminal_id),
FOREIGN KEY (destination_id) REFERENCES terminals(terminal_id)
);

-- 7. TRIPS / SCHEDULES TABLE (Specific departures)
CREATE TABLE trips (
trip_id INT AUTO_INCREMENT PRIMARY KEY,
route_id INT NOT NULL,
bus_id INT NOT NULL,
departure DATETIME NOT NULL,
arrival DATETIME NOT NULL,
status ENUM('Scheduled', 'Boarding', 'Departed', 'Completed', 'Cancelled') DEFAULT 'Scheduled',
FOREIGN KEY (route_id) REFERENCES routes(route_id),
FOREIGN KEY (bus_id) REFERENCES buses(bus_id)
);

-- 8. BOOKINGS / TICKETS TABLE (Passenger reservations)
CREATE TABLE bookings (
booking_id INT AUTO_INCREMENT PRIMARY KEY,
reference_code VARCHAR(30) UNIQUE NOT NULL,
user_id INT NOT NULL, -- Who booked it
trip_id INT NOT NULL,
passenger_name VARCHAR(100) NOT NULL,
seat_number INT NOT NULL,
fare_amount DECIMAL(10,2) NOT NULL,
payment_status ENUM('Pending', 'Paid', 'Refunded') DEFAULT 'Pending',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (user_id) REFERENCES users(user_id),
FOREIGN KEY (trip_id) REFERENCES trips(trip_id)
);

-- 9. CARGO / PARCEL SHIPMENTS TABLE
CREATE TABLE cargo_shipments (
cargo_id INT AUTO_INCREMENT PRIMARY KEY,
tracking_number VARCHAR(30) UNIQUE NOT NULL,
user_id INT NULL, -- Optional if booked while logged in
sender_name VARCHAR(100) NOT NULL,
sender_phone VARCHAR(20) NOT NULL,
recipient_name VARCHAR(100) NOT NULL,
recipient_phone VARCHAR(20) NOT NULL,
origin_id INT NOT NULL,
destination_id INT NOT NULL,
item_description TEXT NOT NULL,
weight_kg DECIMAL(5,2) NOT NULL,
shipping_fee DECIMAL(10,2) NOT NULL,
status ENUM('Accepted', 'In Transit', 'Arrived', 'Claimed') DEFAULT 'Accepted',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
FOREIGN KEY (origin_id) REFERENCES terminals(terminal_id),
FOREIGN KEY (destination_id) REFERENCES terminals(terminal_id)
);