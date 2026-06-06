<?php

class User {
    public $id;
    public $username;
    public $passwordHash;
    public $calorieGoal;

    public function __construct($id, $username, $passwordHash, $calorieGoal) {
        $this->id = (string) $id;
        $this->username = $username;
        $this->passwordHash = $passwordHash;
        $this->calorieGoal = (int) $calorieGoal;
    }

    public static function fromRow(array $row) {
        return new User(
            $row['user_id'],
            $row['email'],
            $row['password_hash'],
            $row['calorie_goal']
        );
    }
}
