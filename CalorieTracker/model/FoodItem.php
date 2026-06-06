<?php

class FoodItem {
    public $id;
    public $name;
    public $caloriesPerServing;
    public $servingSize;
    public $category;

    public function __construct($id, $name, $caloriesPerServing, $servingSize = '', $category = '') {
        $this->id = (string) $id;
        $this->name = $name;
        $this->caloriesPerServing = (float) $caloriesPerServing;
        $this->servingSize = $servingSize;
        $this->category = $category;
    }

    public static function fromRow(array $row) {
        return new FoodItem(
            $row['food_item_id'],
            $row['name'],
            $row['calories_per_serving'],
            $row['serving_size'] ?? '',
            $row['category'] ?? ''
        );
    }
}
