-- Calorie Tracker database setup
-- Run in MySQL: mysql -u root -p < database/schema.sql

CREATE DATABASE IF NOT EXISTS calorie_tracker
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE calorie_tracker;

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    calorie_goal INT NOT NULL DEFAULT 0,
    goal_updated_at DATETIME NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS food_items (
    food_item_id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    calories_per_serving DECIMAL(10, 2) NOT NULL DEFAULT 0,
    serving_size VARCHAR(100) NULL,
    category VARCHAR(100) NULL,
    image_url VARCHAR(500) NULL
);

CREATE TABLE IF NOT EXISTS user_recent_foods (
    user_recent_food_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    food_item_id VARCHAR(50) NULL,
    food_name VARCHAR(255) NOT NULL,
    calories_per_serving DECIMAL(10, 2) NOT NULL,
    serving_size VARCHAR(100) NULL,
    image_url VARCHAR(500) NULL,
    last_used_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_recent (user_id, last_used_at DESC),
    UNIQUE KEY unique_user_recent_food (user_id, food_name, calories_per_serving, serving_size),
    CONSTRAINT fk_user_recent_foods_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE
);

CREATE TABLE IF NOT EXISTS food_entries (
    food_entry_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    food_item_id VARCHAR(50) NULL,
    food_name VARCHAR(255) NOT NULL,
    calories INT NOT NULL,
    calories_per_serving DECIMAL(10, 2) NOT NULL,
    servings DECIMAL(10, 2) NOT NULL DEFAULT 1,
    serving_size VARCHAR(100) NULL,
    date_logged DATE NOT NULL,
    logged_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_user_date (user_id, date_logged),
    CONSTRAINT fk_food_entries_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE,
    CONSTRAINT fk_food_entries_food_item
        FOREIGN KEY (food_item_id) REFERENCES food_items(food_item_id)
        ON DELETE SET NULL
);
