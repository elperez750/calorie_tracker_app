MySQL setup for Calorie Tracker
================================

1. Make sure MySQL is running (MAMP, XAMPP, or local MySQL).

2. Create the database and tables:

   mysql -u root -p < database/schema.sql

3. If upgrading an older version of this project:

   mysql -u root -p calorie_tracker < database/migration_align_plan.sql

4. Update database credentials in:

   CalorieTracker/model/db_config.php

5. Data model (matches class diagram):

   - users          -> User (email used as username)
   - food_items     -> FoodItem (cached from API search)
   - food_entries   -> FoodEntry (daily food log, persisted)
   - users.calorie_goal + goal_updated_at -> CalorieGoal

6. How it works:
   - Register/login with hashed passwords
   - Calorie goal is saved per user and pre-filled on edit
   - Food search uses the FatSecret API and shows serving size
   - Adding food creates a FoodEntry in MySQL for today
   - "Recently Eaten" shows foods from your past log entries
