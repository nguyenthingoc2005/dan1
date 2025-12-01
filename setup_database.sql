-- ==============================================================================
-- TOUR MANAGEMENT SYSTEM - DATABASE INITIALIZATION SCRIPT
-- ==============================================================================
-- Version: 2.0
-- Date: 2024-12-01
-- Author: Senior PHP Developer (Vibe Coding)
-- 
-- Tạo database và tất cả 28 bảng theo schema
-- ==============================================================================

DROP DATABASE IF EXISTS tour_management;
CREATE DATABASE tour_management CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE tour_management;
SET time_zone = '+07:00';

-- ==============================================================================
-- MODULE 1: USERS & ROLES
-- ==============================================================================

CREATE TABLE roles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(50) NOT NULL UNIQUE,
    display_name VARCHAR(100) NOT NULL,
    description TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO roles (name, display_name, description) VALUES
('admin', 'Quản trị viên', 'Quản lý toàn bộ hệ thống'),
('staff', 'Nhân viên', 'Tạo tour, booking, quản lý khách hàng'),
('guide', 'Hướng dẫn viên', 'Điều hành tour');

CREATE TABLE users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    role_id INT NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    phone VARCHAR(20),
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other'),
    address TEXT,
    avatar VARCHAR(255),
    status ENUM('active', 'inactive', 'suspended') DEFAULT 'active',
    last_login TIMESTAMP NULL,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (role_id) REFERENCES roles(id),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Tạo user admin mẫu (password: Admin@123)
INSERT INTO users (role_id, email, password, full_name, phone, status) VALUES
(1, 'admin@company.com', '$2y$10$YourHashedPasswordHere', 'Admin User', '0901234567', 'active');

CREATE TABLE password_resets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(100) NOT NULL,
    token VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    expires_at TIMESTAMP NOT NULL,
    used_at TIMESTAMP NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- MODULE 2: CATEGORIES & DESTINATIONS
-- ==============================================================================

CREATE TABLE categories (
    id INT PRIMARY KEY AUTO_INCREMENT,
    parent_id INT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    display_order INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_by INT,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (parent_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE destinations (
    id INT PRIMARY KEY AUTO_INCREMENT,
    category_id INT,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    locations TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_by INT,
    updated_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (updated_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE destination_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    destination_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    caption VARCHAR(255),
    is_primary BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- MODULE 3: SERVICES & SUPPLIERS
-- ==============================================================================

CREATE TABLE service_types (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100) NOT NULL,
    code VARCHAR(50) UNIQUE,
    description TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO service_types (name, code, description) VALUES
('Khách sạn', 'HOTEL', 'Dịch vụ lưu trú'),
('Nhà hàng', 'RESTAURANT', 'Dịch vụ ăn uống'),
('Phương tiện', 'VEHICLE', 'Dịch vụ vận chuyển'),
('Vé tham quan', 'TICKET', 'Vé các điểm tham quan'),
('Hướng dẫn viên', 'GUIDE', 'Dịch vụ hướng dẫn'),
('Bảo hiểm', 'INSURANCE', 'Bảo hiểm du lịch');

CREATE TABLE suppliers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    supplier_code VARCHAR(50) UNIQUE,
    company_name VARCHAR(200) NOT NULL,
    contact_person VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    address TEXT,
    tax_code VARCHAR(50),
    bank_name VARCHAR(100),
    bank_account VARCHAR(50),
    bank_holder VARCHAR(100),
    contract_start DATE,
    contract_end DATE,
    payment_terms VARCHAR(200),
    notes TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    service_code VARCHAR(50) UNIQUE,
    service_type_id INT NOT NULL,
    supplier_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    unit VARCHAR(50),
    estimated_price DECIMAL(15,2),
    notes TEXT,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (service_type_id) REFERENCES service_types(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tour_services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tour_id INT NOT NULL,
    service_id INT NOT NULL,
    service_name VARCHAR(200),
    calculation_type ENUM('per_person', 'per_group', 'per_day', 'fixed') DEFAULT 'per_person',
    fixed_quantity INT DEFAULT 1,
    group_size INT,
    unit_price DECIMAL(15,2) NOT NULL,
    unit VARCHAR(50),
    notes TEXT,
    is_included_in_price BOOLEAN DEFAULT TRUE,
    FOREIGN KEY (service_id) REFERENCES services(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE booking_services (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    service_id INT NOT NULL,
    supplier_id INT NOT NULL,
    service_name VARCHAR(200),
    quantity INT NOT NULL,
    unit VARCHAR(50),
    unit_price DECIMAL(15,2),
    total_price DECIMAL(15,2),
    service_date DATE,
    from_date DATE,
    to_date DATE,
    payment_status ENUM('pending', 'partial', 'paid') DEFAULT 'pending',
    paid_amount DECIMAL(15,2) DEFAULT 0,
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (service_id) REFERENCES services(id),
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE supplier_payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    payment_code VARCHAR(50) UNIQUE,
    supplier_id INT NOT NULL,
    booking_id INT NULL,
    amount DECIMAL(15,2) NOT NULL,
    payment_method ENUM('cash', 'bank_transfer', 'check') DEFAULT 'bank_transfer',
    payment_date DATE NOT NULL,
    invoice_number VARCHAR(100),
    receipt_file VARCHAR(255),
    notes TEXT,
    status ENUM('pending', 'completed', 'cancelled') DEFAULT 'pending',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (supplier_id) REFERENCES suppliers(id),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE supplier_payment_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    payment_id INT NOT NULL,
    booking_service_id INT NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    notes TEXT,
    FOREIGN KEY (payment_id) REFERENCES supplier_payments(id) ON DELETE CASCADE,
    FOREIGN KEY (booking_service_id) REFERENCES booking_services(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- MODULE 4: TOURS
-- ==============================================================================

CREATE TABLE tours (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tour_code VARCHAR(50) UNIQUE,
    category_id INT,
    name VARCHAR(200) NOT NULL,
    thumbnail VARCHAR(255),
    introduction TEXT,
    description TEXT,
    duration_days INT NOT NULL,
    duration_nights INT NOT NULL,
    departure_location VARCHAR(200),
    min_participants INT DEFAULT 15,
    max_participants INT DEFAULT 45,
    price_based_on_pax INT DEFAULT 40,
    adult_price DECIMAL(15,2) NOT NULL,
    child_price DECIMAL(15,2) NOT NULL,
    infant_price DECIMAL(15,2) DEFAULT 0,
    estimated_cost_per_person DECIMAL(15,2),
    markup_percentage DECIMAL(5,2) DEFAULT 20.00,
    deposit_percentage DECIMAL(5,2) DEFAULT 30.00,
    tour_type ENUM('public', 'custom') DEFAULT 'public',
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    approved_by INT,
    approved_at TIMESTAMP NULL,
    rejection_reason TEXT,
    status ENUM('active', 'inactive', 'draft') DEFAULT 'draft',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tour_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tour_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    caption VARCHAR(255),
    is_primary BOOLEAN DEFAULT FALSE,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tour_highlights (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tour_id INT NOT NULL,
    highlight TEXT NOT NULL,
    display_order INT DEFAULT 0,
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tour_included_excluded (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tour_id INT NOT NULL,
    type ENUM('included', 'excluded') NOT NULL,
    item TEXT NOT NULL,
    display_order INT DEFAULT 0,
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tour_faqs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tour_id INT NOT NULL,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    display_order INT DEFAULT 0,
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE itineraries (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tour_id INT NOT NULL,
    destination_id INT,
    day_number INT NOT NULL,
    title VARCHAR(200),
    description TEXT,
    meals JSON,
    accommodation VARCHAR(200),
    arrival_time TIME,
    departure_time TIME,
    display_order INT DEFAULT 0,
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE,
    FOREIGN KEY (destination_id) REFERENCES destinations(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE policies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(200) NOT NULL,
    description TEXT,
    policy_type VARCHAR(50),
    content TEXT NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE tour_policies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    tour_id INT NOT NULL,
    policy_id INT NOT NULL,
    FOREIGN KEY (tour_id) REFERENCES tours(id) ON DELETE CASCADE,
    FOREIGN KEY (policy_id) REFERENCES policies(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- MODULE 5: CUSTOMERS
-- ==============================================================================

CREATE TABLE customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    customer_code VARCHAR(50) UNIQUE,
    full_name VARCHAR(100) NOT NULL,
    email VARCHAR(100),
    phone VARCHAR(20) NOT NULL,
    date_of_birth DATE,
    gender ENUM('male', 'female', 'other'),
    id_card VARCHAR(50),
    passport VARCHAR(50),
    nationality VARCHAR(50) DEFAULT 'Vietnam',
    address TEXT,
    customer_type ENUM('individual', 'group', 'corporate') DEFAULT 'individual',
    source ENUM('phone', 'email', 'facebook', 'zalo', 'walk_in', 'other'),
    special_requirements TEXT,
    notes TEXT,
    total_bookings INT DEFAULT 0,
    total_spent DECIMAL(15,2) DEFAULT 0,
    status ENUM('active', 'inactive', 'blacklist') DEFAULT 'active',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE customer_import_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255),
    imported_by INT,
    total_rows INT DEFAULT 0,
    success_count INT DEFAULT 0,
    error_count INT DEFAULT 0,
    error_details JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (imported_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- MODULE 6: BOOKINGS
-- ==============================================================================

CREATE TABLE cancellation_policies (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(100),
    description TEXT,
    days_before INT NOT NULL,
    fee_percentage DECIMAL(5,2) NOT NULL,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO cancellation_policies (name, description, days_before, fee_percentage) VALUES
('Hủy sớm', 'Hủy trước 15 ngày', 15, 10.00),
('Hủy trung bình', 'Hủy 7-15 ngày', 7, 30.00),
('Hủy gần', 'Hủy 3-6 ngày', 3, 50.00),
('Hủy rất gần', 'Hủy dưới 3 ngày', 0, 100.00);

CREATE TABLE discount_codes (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(50) NOT NULL UNIQUE,
    name VARCHAR(200),
    discount_type ENUM('percentage', 'fixed') NOT NULL,
    discount_value DECIMAL(15,2) NOT NULL,
    min_purchase DECIMAL(15,2) DEFAULT 0,
    start_date DATE,
    end_date DATE,
    usage_limit INT DEFAULT 0,
    used_count INT DEFAULT 0,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE bookings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_code VARCHAR(50) UNIQUE,
    tour_id INT NOT NULL,
    customer_id INT NOT NULL,
    adult_count INT DEFAULT 0,
    child_count INT DEFAULT 0,
    infant_count INT DEFAULT 0,
    start_date DATE NOT NULL,
    end_date DATE NOT NULL,
    total_amount DECIMAL(15,2) NOT NULL,
    discount_code VARCHAR(50),
    discount_amount DECIMAL(15,2) DEFAULT 0,
    final_amount DECIMAL(15,2) NOT NULL,
    deposit_amount DECIMAL(15,2) DEFAULT 0,
    paid_amount DECIMAL(15,2) DEFAULT 0,
    remaining_amount DECIMAL(15,2) DEFAULT 0,
    payment_status ENUM('unpaid', 'partial', 'paid', 'refunded') DEFAULT 'unpaid',
    approval_status ENUM('pending', 'approved', 'rejected', 'cancelled') DEFAULT 'pending',
    approved_by INT,
    approved_at TIMESTAMP NULL,
    rejection_reason TEXT,
    cancellation_date DATE,
    cancellation_reason TEXT,
    cancellation_policy_id INT,
    cancellation_fee DECIMAL(15,2) DEFAULT 0,
    refund_amount DECIMAL(15,2) DEFAULT 0,
    source ENUM('phone', 'email', 'facebook', 'zalo', 'walk_in', 'other'),
    special_requests TEXT,
    notes TEXT,
    internal_notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (tour_id) REFERENCES tours(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (cancellation_policy_id) REFERENCES cancellation_policies(id) ON DELETE SET NULL,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE booking_customers (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    customer_id INT NOT NULL,
    age_type ENUM('adult', 'child', 'infant') DEFAULT 'adult',
    is_primary BOOLEAN DEFAULT FALSE,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (customer_id) REFERENCES customers(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE booking_status_history (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    old_status VARCHAR(50),
    new_status VARCHAR(50),
    changed_by INT,
    reason TEXT,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- MODULE 7: PAYMENTS
-- ==============================================================================

CREATE TABLE payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    payment_method ENUM('cash', 'bank_transfer', 'credit_card', 'other') DEFAULT 'cash',
    amount DECIMAL(15,2) NOT NULL,
    payment_type ENUM('deposit', 'installment', 'full', 'refund') DEFAULT 'deposit',
    transaction_id VARCHAR(100),
    receipt_number VARCHAR(100),
    payment_date DATE NOT NULL,
    status ENUM('pending', 'completed', 'failed', 'cancelled') DEFAULT 'completed',
    notes TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE payment_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    payment_id INT NOT NULL,
    action ENUM('created', 'updated', 'deleted', 'refunded') NOT NULL,
    old_values JSON,
    new_values JSON,
    changed_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE CASCADE,
    FOREIGN KEY (changed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE invoices (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    invoice_number VARCHAR(100) UNIQUE,
    invoice_date DATE NOT NULL,
    subtotal DECIMAL(15,2) NOT NULL,
    tax_amount DECIMAL(15,2) DEFAULT 0,
    discount_amount DECIMAL(15,2) DEFAULT 0,
    total_amount DECIMAL(15,2) NOT NULL,
    status ENUM('draft', 'issued', 'paid', 'cancelled') DEFAULT 'draft',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE refunds (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    payment_id INT,
    refund_amount DECIMAL(15,2) NOT NULL,
    reason TEXT,
    status ENUM('pending', 'approved', 'completed', 'rejected') DEFAULT 'pending',
    processed_by INT,
    processed_at TIMESTAMP NULL,
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id) ON DELETE CASCADE,
    FOREIGN KEY (payment_id) REFERENCES payments(id) ON DELETE SET NULL,
    FOREIGN KEY (processed_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- MODULE 8: OPERATIONS
-- ==============================================================================

CREATE TABLE tour_assignments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    guide_id INT NOT NULL,
    assignment_date DATE NOT NULL,
    salary_amount DECIMAL(15,2),
    salary_status ENUM('pending', 'paid') DEFAULT 'pending',
    paid_date DATE,
    notes TEXT,
    status ENUM('assigned', 'in_progress', 'completed', 'cancelled') DEFAULT 'assigned',
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    FOREIGN KEY (guide_id) REFERENCES users(id),
    FOREIGN KEY (created_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE customer_checkins (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    customer_id INT NOT NULL,
    checkin_time TIMESTAMP NOT NULL,
    status ENUM('present', 'absent', 'late') DEFAULT 'present',
    notes TEXT,
    checked_by INT,
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    FOREIGN KEY (customer_id) REFERENCES customers(id),
    FOREIGN KEY (checked_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE journals (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    guide_id INT NOT NULL,
    journal_date DATE NOT NULL,
    day_number INT,
    title VARCHAR(200),
    content TEXT,
    weather VARCHAR(100),
    highlights TEXT,
    issues TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    FOREIGN KEY (guide_id) REFERENCES users(id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE journal_images (
    id INT PRIMARY KEY AUTO_INCREMENT,
    journal_id INT NOT NULL,
    image_url VARCHAR(255) NOT NULL,
    caption TEXT,
    display_order INT DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (journal_id) REFERENCES journals(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE incurred_expenses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    booking_id INT NOT NULL,
    expense_date DATE NOT NULL,
    category VARCHAR(100),
    description TEXT NOT NULL,
    amount DECIMAL(15,2) NOT NULL,
    receipt_file VARCHAR(255),
    reported_by INT,
    approved_by INT,
    approval_status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (booking_id) REFERENCES bookings(id),
    FOREIGN KEY (reported_by) REFERENCES users(id) ON DELETE SET NULL,
    FOREIGN KEY (approved_by) REFERENCES users(id) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- MODULE 9: EMAIL
-- ==============================================================================

CREATE TABLE email_templates (
    id INT PRIMARY KEY AUTO_INCREMENT,
    code VARCHAR(100) NOT NULL UNIQUE,
    name VARCHAR(200) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT NOT NULL,
    variables JSON,
    status ENUM('active', 'inactive') DEFAULT 'active',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE email_logs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email_to VARCHAR(255) NOT NULL,
    subject VARCHAR(255) NOT NULL,
    body TEXT,
    email_type VARCHAR(50),
    related_id INT,
    related_type VARCHAR(50),
    sent_at TIMESTAMP NULL,
    status ENUM('pending', 'sent', 'failed') DEFAULT 'pending',
    error_message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- MODULE 10: VEHICLES (Optional)
-- ==============================================================================

CREATE TABLE vehicles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    vehicle_code VARCHAR(50) UNIQUE,
    vehicle_type ENUM('bus_45', 'bus_29', 'bus_16', 'car_7', 'car_4') NOT NULL,
    license_plate VARCHAR(20) NOT NULL,
    capacity INT NOT NULL,
    status ENUM('active', 'maintenance', 'inactive') DEFAULT 'active',
    notes TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ==============================================================================
-- CREATE INDEXES FOR BETTER PERFORMANCE
-- ==============================================================================

-- Users & Auth
CREATE INDEX idx_users_email ON users(email);
CREATE INDEX idx_users_role_id ON users(role_id);
CREATE INDEX idx_users_status ON users(status);

-- Tours
CREATE INDEX idx_tours_approval_status ON tours(approval_status);
CREATE INDEX idx_tours_status ON tours(status);
CREATE INDEX idx_tours_category_id ON tours(category_id);
CREATE INDEX idx_tours_created_by ON tours(created_by);

-- Bookings
CREATE INDEX idx_bookings_approval_status ON bookings(approval_status);
CREATE INDEX idx_bookings_payment_status ON bookings(payment_status);
CREATE INDEX idx_bookings_tour_id ON bookings(tour_id);
CREATE INDEX idx_bookings_customer_id ON bookings(customer_id);
CREATE INDEX idx_bookings_start_date ON bookings(start_date);

-- Customers
CREATE INDEX idx_customers_phone ON customers(phone);
CREATE INDEX idx_customers_email ON customers(email);
CREATE INDEX idx_customers_status ON customers(status);

-- Payments
CREATE INDEX idx_payments_booking_id ON payments(booking_id);
CREATE INDEX idx_payments_payment_date ON payments(payment_date);

-- Supplier Payments
CREATE INDEX idx_supplier_payments_supplier_id ON supplier_payments(supplier_id);
CREATE INDEX idx_supplier_payments_status ON supplier_payments(status);

-- Tour Assignments
CREATE INDEX idx_tour_assignments_booking_id ON tour_assignments(booking_id);
CREATE INDEX idx_tour_assignments_guide_id ON tour_assignments(guide_id);

-- Expenses
CREATE INDEX idx_incurred_expenses_booking_id ON incurred_expenses(booking_id);
CREATE INDEX idx_incurred_expenses_approval_status ON incurred_expenses(approval_status);

-- ==============================================================================
-- END OF SCRIPT
-- ==============================================================================
