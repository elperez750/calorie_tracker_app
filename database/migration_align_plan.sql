-- Run if upgrading an existing database:
-- mysql -u root -p calorie_tracker < database/migration_align_plan.sql

USE calorie_tracker;

ALTER TABLE users
    ADD COLUMN goal_updated_at DATETIME NULL;

DROP TABLE IF EXISTS recent_foods;

CREATE TABLE IF NOT EXISTS food_items (
    food_item_id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    calories_per_serving DECIMAL(10, 2) NOT NULL DEFAULT 0,
    serving_size VARCHAR(100) NULL,
    category VARCHAR(100) NULL
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
