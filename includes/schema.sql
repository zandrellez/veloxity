-- ==========================================
-- 0. CUSTOM ENUM TYPES FOR POSTGRESQL
-- ==========================================
CREATE TYPE role_type AS ENUM ('customer', 'operator_admin', 'super_admin');
CREATE TYPE auth_provider_type AS ENUM ('local', 'google', 'facebook');
CREATE TYPE passenger_type_enum AS ENUM ('Regular', 'Senior', 'PWD', 'Student');
CREATE TYPE trip_status_type AS ENUM ('Scheduled', 'Boarding', 'Departed', 'Completed', 'Cancelled');
CREATE TYPE payment_status_type AS ENUM ('Pending', 'Paid', 'Refunded');
CREATE TYPE cargo_status_type AS ENUM ('Accepted', 'In Transit', 'Arrived', 'Claimed');

-- ==========================================
-- 1. USERS TABLE (System accounts)
-- ==========================================
CREATE TABLE users (
    user_id SERIAL PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(255),
    contact VARCHAR(20) NOT NULL,
    role role_type DEFAULT 'customer',
    verification_token VARCHAR(255) NULL,
    token_expires_at TIMESTAMP NULL,
    is_verified SMALLINT DEFAULT 0,
    otp_code VARCHAR(6) NULL,
    otp_expires_at TIMESTAMP NULL,
    google_id VARCHAR(255) NULL,
    facebook_id VARCHAR(255) NULL,
    auth_provider auth_provider_type DEFAULT 'local',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- ==========================================
-- 2. SAVED PASSENGERS / FAMILY PROFILES
-- ==========================================
CREATE TABLE saved_passengers (
    passenger_id SERIAL PRIMARY KEY,
    user_id INT NOT NULL, 
    name VARCHAR(100) NOT NULL,
    age INT,
    sex VARCHAR(10),
    contact VARCHAR(20),
    passenger_type passenger_type_enum DEFAULT 'Regular',
    discount_id VARCHAR(50) NULL,
    FOREIGN KEY (user_id) REFERENCES users(user_id) ON DELETE CASCADE
);

-- ==========================================
-- 3. TERMINALS TABLE
-- ==========================================
CREATE TABLE terminals (
    terminal_id SERIAL PRIMARY KEY,
    terminal_name VARCHAR(150) NOT NULL,
    city VARCHAR(100) NOT NULL
);

-- ==========================================
-- 4. OPERATORS TABLE
-- ==========================================
CREATE TABLE operators (
    operator_id SERIAL PRIMARY KEY,
    operator_name VARCHAR(150) NOT NULL,
    permit_number VARCHAR(100) NOT NULL,
    headquarters_address VARCHAR(255) NOT NULL,
    user_id INT NULL,
    contact_email VARCHAR(100),
    contact_phone VARCHAR(20),
    acc_status VARCHAR(20) DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id)
);

-- ==========================================
-- 5. BUS / FLEET TABLE
-- ==========================================
CREATE TABLE buses (
    bus_id SERIAL PRIMARY KEY,
    operator_id INT NOT NULL,
    plate_number VARCHAR(20) UNIQUE NOT NULL,
    seat_capacity INT NOT NULL,
    FOREIGN KEY (operator_id) REFERENCES operators(operator_id) ON DELETE CASCADE
);

-- ==========================================
-- 6. ROUTES TABLE
-- ==========================================
CREATE TABLE routes (
    route_id SERIAL PRIMARY KEY,
    origin_id INT NOT NULL,
    destination_id INT NOT NULL,
    distance_km DECIMAL(6,2) NOT NULL,
    base_fare DECIMAL(10,2) NOT NULL,
    FOREIGN KEY (origin_id) REFERENCES terminals(terminal_id),
    FOREIGN KEY (destination_id) REFERENCES terminals(terminal_id)
);

-- ==========================================
-- 7. TRIPS / SCHEDULES TABLE
-- ==========================================
CREATE TABLE trips (
    trip_id SERIAL PRIMARY KEY,
    route_id INT NOT NULL,
    bus_id INT NOT NULL,
    departure TIMESTAMP NOT NULL,
    arrival TIMESTAMP NOT NULL,
    status trip_status_type DEFAULT 'Scheduled',
    FOREIGN KEY (route_id) REFERENCES routes(route_id),
    FOREIGN KEY (bus_id) REFERENCES buses(bus_id)
);

-- ==========================================
-- 8. BOOKINGS / TICKETS TABLE
-- ==========================================
CREATE TABLE bookings (
    booking_id SERIAL PRIMARY KEY,
    reference_code VARCHAR(30) UNIQUE NOT NULL,
    user_id INT NOT NULL, 
    trip_id INT NOT NULL,
    passenger_name VARCHAR(100) NOT NULL,
    seat_number INT NOT NULL,
    fare_amount DECIMAL(10,2) NOT NULL,
    payment_status payment_status_type DEFAULT 'Pending',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(user_id),
    FOREIGN KEY (trip_id) REFERENCES trips(trip_id)
);

-- ==========================================
-- 9. CARGO / PARCEL SHIPMENTS TABLE
-- ==========================================
CREATE TABLE cargo_shipments (
    cargo_id SERIAL PRIMARY KEY,
    tracking_number VARCHAR(30) UNIQUE NOT NULL,
    user_id INT NULL, 
    sender_name VARCHAR(100) NOT NULL,
    sender_phone VARCHAR(20) NOT NULL,
    recipient_name VARCHAR(100) NOT NULL,
    recipient_phone VARCHAR(20) NOT NULL,
    origin_id INT NOT NULL,
    destination_id INT NOT NULL,
    item_description TEXT NOT NULL,
    weight_kg DECIMAL(5,2) NOT NULL,
    shipping_fee DECIMAL(10,2) NOT NULL,
    status cargo_status_type DEFAULT 'Accepted',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (origin_id) REFERENCES terminals(terminal_id),
    FOREIGN KEY (destination_id) REFERENCES terminals(terminal_id)
);