-- Run this if you already created the old database without users:
-- mysql -u root -p calorie_tracker < database/migration_add_users.sql

USE calorie_tracker;

CREATE TABLE IF NOT EXISTS users (
    user_id INT AUTO_INCREMENT PRIMARY KEY,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    calorie_goal INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
);

DROP TABLE IF EXISTS recent_foods;

CREATE TABLE recent_foods (
    recent_food_id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    food_name VARCHAR(255) NOT NULL,
    calories_per_serving DECIMAL(10, 2) NOT NULL DEFAULT 0,
    fatsecret_food_id VARCHAR(50) NULL,
    last_searched_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_user_last_searched (user_id, last_searched_at DESC),
    UNIQUE KEY unique_user_food (user_id, food_name),
    CONSTRAINT fk_recent_foods_user
        FOREIGN KEY (user_id) REFERENCES users(user_id)
        ON DELETE CASCADE
);
