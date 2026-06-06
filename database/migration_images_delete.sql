USE calorie_tracker;

ALTER TABLE food_items
    ADD COLUMN image_url VARCHAR(500) NULL;

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
