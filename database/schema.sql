SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS cinemanoir CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE cinemanoir;

-- Admin users (for management dashboard)
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff') DEFAULT 'admin',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Movies
DROP TABLE IF EXISTS movies;
CREATE TABLE movies (
    id INT AUTO_INCREMENT PRIMARY KEY,
    title VARCHAR(255) NOT NULL,
    description TEXT,
    duration INT, -- in minutes
    genre VARCHAR(100),
    rating VARCHAR(10), -- e.g. 16+, 12+
    release_date DATE,
    director VARCHAR(255),
    cast TEXT, -- JSON string or comma-separated list
    trailer_url VARCHAR(255),
    poster_path VARCHAR(255),
    user_rating DECIMAL(3,1) DEFAULT 8.5,
    status ENUM('now playing', 'coming soon', 'archived') DEFAULT 'now playing',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- Halls
DROP TABLE IF EXISTS halls;
CREATE TABLE halls (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(50) NOT NULL,
    capacity INT NOT NULL
) ENGINE=InnoDB;

-- Seats
CREATE TABLE IF NOT EXISTS seats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    hall_id INT NOT NULL,
    row_num INT NOT NULL,
    seat_num INT NOT NULL,
    type ENUM('standard', 'vip') DEFAULT 'standard',
    FOREIGN KEY (hall_id) REFERENCES halls(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Showtimes
CREATE TABLE IF NOT EXISTS showtimes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    movie_id INT NOT NULL,
    hall_id INT NOT NULL,
    start_time DATETIME NOT NULL,
    base_price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (movie_id) REFERENCES movies(id) ON DELETE CASCADE,
    FOREIGN KEY (hall_id) REFERENCES halls(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Promo Codes
CREATE TABLE IF NOT EXISTS promo_codes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    code VARCHAR(20) NOT NULL UNIQUE,
    discount_percent INT NOT NULL,
    valid_until DATETIME,
    is_active BOOLEAN DEFAULT TRUE
) ENGINE=InnoDB;

-- Reservations
CREATE TABLE IF NOT EXISTS reservations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(64) NOT NULL UNIQUE,
    showtime_id INT NOT NULL,
    customer_name VARCHAR(100) NOT NULL,
    customer_email VARCHAR(100) NOT NULL,
    total_price DECIMAL(10, 2) NOT NULL,
    promo_code_id INT DEFAULT NULL,
    payment_method VARCHAR(50),
    status ENUM('pending', 'confirmed', 'cancelled') DEFAULT 'confirmed',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (showtime_id) REFERENCES showtimes(id) ON DELETE CASCADE,
    FOREIGN KEY (promo_code_id) REFERENCES promo_codes(id) ON DELETE SET NULL
) ENGINE=InnoDB;

-- Reserved Seats (Link table for reservations)
CREATE TABLE IF NOT EXISTS reserved_seats (
    id INT AUTO_INCREMENT PRIMARY KEY,
    uid VARCHAR(64) NOT NULL UNIQUE, -- Unique code for this specific ticket
    reservation_id INT NOT NULL,
    seat_id INT NOT NULL,
    ticket_type ENUM('standard', 'student', 'senior') DEFAULT 'standard',
    price DECIMAL(10, 2) NOT NULL,
    FOREIGN KEY (reservation_id) REFERENCES reservations(id) ON DELETE CASCADE,
    FOREIGN KEY (seat_id) REFERENCES seats(id) ON DELETE CASCADE
) ENGINE=InnoDB;
-- Newsletters (Email subscription)
CREATE TABLE IF NOT EXISTS newsletters (
    id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(100) NOT NULL UNIQUE,
    subscribed_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
