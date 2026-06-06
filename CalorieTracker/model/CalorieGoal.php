<?php

class CalorieGoal {
    public $userId;
    public $targetCalories;
    public $updatedAt;

    public function __construct($userId, $targetCalories, $updatedAt = null) {
        $this->userId = (string) $userId;
        $this->targetCalories = (int) $targetCalories;
        $this->updatedAt = $updatedAt;
    }

    public static function fromUserRow(array $row) {
        return new CalorieGoal(
            $row['user_id'],
            $row['calorie_goal'],
            $row['goal_updated_at'] ?? null
        );
    }
}
